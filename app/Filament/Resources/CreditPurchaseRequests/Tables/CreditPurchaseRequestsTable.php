<?php

namespace App\Filament\Resources\CreditPurchaseRequests\Tables;

use App\Models\CreditPackagePurchase;
use App\Models\CreditPurchaseRequest;
use App\Models\UserCredit;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class CreditPurchaseRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('package.name')
                    ->label('Paquete')
                    ->searchable(),
                TextColumn::make('quoted_price_display')
                    ->label('Monto cotizado')
                    ->getStateUsing(function (CreditPurchaseRequest $record): HtmlString {
                        $final = $record->quoted_final_price ?? $record->package?->price;
                        if ($final === null || $final === '') {
                            return new HtmlString('—');
                        }

                        $final = (float) $final;
                        $base = $record->quoted_base_price !== null && $record->quoted_base_price !== ''
                            ? (float) $record->quoted_base_price
                            : $final;

                        return new HtmlString(view('components.credit-package-price-display', [
                            'basePrice' => $base,
                            'finalPrice' => $final,
                            'variant' => 'table',
                        ])->render());
                    })
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('payment_method')
                    ->label('Método de pago')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        CreditPurchaseRequest::METHOD_TRANSFER => 'Transferencia',
                        CreditPurchaseRequest::METHOD_CASH => 'Efectivo',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        CreditPurchaseRequest::STATUS_PENDING => 'warning',
                        CreditPurchaseRequest::STATUS_APPROVED => 'success',
                        CreditPurchaseRequest::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        CreditPurchaseRequest::STATUS_PENDING => 'Pendiente',
                        CreditPurchaseRequest::STATUS_APPROVED => 'Aprobada',
                        CreditPurchaseRequest::STATUS_REJECTED => 'Rechazada',
                        default => $state,
                    }),
                TextColumn::make('requestedTenant.name')
                    ->label('Sucursal solicitada')
                    ->placeholder('N/A'),
                TextColumn::make('requested_date')
                    ->label('Fecha solicitada')
                    ->date('d/m/Y')
                    ->placeholder('N/A'),
                TextColumn::make('requested_time_slot')
                    ->label('Hora solicitada')
                    ->time('H:i')
                    ->placeholder('N/A'),
                TextColumn::make('created_at')
                    ->label('Solicitada')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('reviewer.name')
                    ->label('Revisada por')
                    ->placeholder('N/A'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        CreditPurchaseRequest::STATUS_PENDING => 'Pendiente',
                        CreditPurchaseRequest::STATUS_APPROVED => 'Aprobada',
                        CreditPurchaseRequest::STATUS_REJECTED => 'Rechazada',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Método')
                    ->options([
                        CreditPurchaseRequest::METHOD_TRANSFER => 'Transferencia',
                        CreditPurchaseRequest::METHOD_CASH => 'Efectivo',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Aprobar y acreditar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CreditPurchaseRequest $record): bool => $record->status === CreditPurchaseRequest::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar pago y acreditar créditos')
                    ->modalDescription('Esta acción acredita los créditos al cliente y no agenda automáticamente su cita.')
                    ->action(function (CreditPurchaseRequest $record): void {
                        DB::transaction(function () use ($record): void {
                            $record->refresh();

                            if ($record->status !== CreditPurchaseRequest::STATUS_PENDING) {
                                return;
                            }

                            $user = $record->user;
                            $package = $record->package;

                            $hasActiveCredits = $user->credits()
                                ->where('balance', '>', 0)
                                ->where('expires_at', '>', now())
                                ->exists();

                            if ($hasActiveCredits) {
                                $record->update([
                                    'status' => CreditPurchaseRequest::STATUS_REJECTED,
                                    'reviewed_by' => Auth::id(),
                                    'reviewed_at' => now(),
                                    'review_notes' => 'Rechazada automáticamente: el cliente ya tenía créditos activos al revisar.',
                                ]);

                                return;
                            }

                            if ($package->is_one_time_purchase) {
                                $purchase = CreditPackagePurchase::firstOrCreate([
                                    'user_id' => $user->id,
                                    'credit_package_id' => $package->id,
                                ]);

                                if (! $purchase->wasRecentlyCreated) {
                                    $record->update([
                                        'status' => CreditPurchaseRequest::STATUS_REJECTED,
                                        'reviewed_by' => Auth::id(),
                                        'reviewed_at' => now(),
                                        'review_notes' => 'Rechazada: paquete de compra única ya adquirido previamente.',
                                    ]);

                                    return;
                                }
                            }

                            $tenantId = $record->requested_tenant_id ?? $user->tenants()->value('tenants.id');
                            if (! $tenantId) {
                                $tenantId = 1;
                            }

                            UserCredit::create([
                                'user_id' => $user->id,
                                'tenant_id' => $tenantId,
                                'balance' => $package->credits_amount,
                                'expires_at' => now()->addDays(30),
                                'is_special' => false,
                            ]);

                            $record->update([
                                'status' => CreditPurchaseRequest::STATUS_APPROVED,
                                'reviewed_by' => Auth::id(),
                                'reviewed_at' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Solicitud procesada')
                            ->body('La solicitud fue revisada. Si cumplía reglas, los créditos ya fueron acreditados.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CreditPurchaseRequest $record): bool => $record->status === CreditPurchaseRequest::STATUS_PENDING)
                    ->form([
                        Textarea::make('review_notes')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->maxLength(1500),
                    ])
                    ->action(function (CreditPurchaseRequest $record, array $data): void {
                        $record->update([
                            'status' => CreditPurchaseRequest::STATUS_REJECTED,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                            'review_notes' => $data['review_notes'],
                        ]);

                        Notification::make()
                            ->title('Solicitud rechazada')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
