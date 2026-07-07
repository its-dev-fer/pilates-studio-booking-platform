<?php

namespace App\Filament\Resources\Users\Tables;

use App\Mail\WelcomeUserMail;
use App\Models\Tenant;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with([
                'credits' => fn ($creditsQuery) => $creditsQuery
                    ->where('balance', '>', 0)
                    ->where('expires_at', '>', now()),
            ]))
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('last_name')->searchable(),
                TextColumn::make('branch_credits')
                    ->label('Créditos por sucursal')
                    ->state(fn (User $record): array => self::branchCreditLabels($record))
                    ->badge()
                    ->color(fn (string $state): string => self::branchCreditBadgeColor($state))
                    ->placeholder('Sin créditos activos'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')
                    ->label('Perfil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'empleado' => 'warning',
                        'cliente' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('tenants.name')
                    ->label('Sucursales')
                    ->badge()
                    ->limitList(2),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('manageCredits')
                    ->label('Gestionar créditos')
                    ->icon('heroicon-o-ticket')
                    ->color('primary')
                    ->visible(fn (): bool => auth()->user()?->hasRole('admin') ?? false)
                    ->modalHeading('Gestionar créditos')
                    ->modalDescription(fn (User $record): string => "Cliente: {$record->name} {$record->last_name}")
                    ->modalSubmitActionLabel('Confirmar cambios')
                    ->form(fn (User $record): array => [
                        Placeholder::make('branch_balances')
                            ->label('Créditos activos por sucursal')
                            ->content(new HtmlString(self::branchBalancesHtml($record))),
                        Select::make('operation')
                            ->label('Acción')
                            ->options([
                                'assign' => 'Asignar créditos',
                                'remove' => 'Quitar créditos',
                            ])
                            ->default('assign')
                            ->required()
                            ->live(),
                        Select::make('tenant_ids')
                            ->label('Sucursales')
                            ->multiple()
                            ->required()
                            ->options(function (Get $get) use ($record) {
                                $tenants = self::orderedTenants();

                                if ($get('operation') === 'remove') {
                                    return $tenants
                                        ->filter(fn (Tenant $tenant): bool => $record->activeCredits($tenant->id) > 0)
                                        ->pluck('name', 'id');
                                }

                                return $tenants->pluck('name', 'id');
                            })
                            ->helperText(fn (Get $get): string => $get('operation') === 'remove'
                                ? 'Solo se muestran sucursales con créditos activos. Se quitará el mismo monto en cada una seleccionada.'
                                : 'Se acreditará el mismo monto en cada sucursal seleccionada.'),
                        TextInput::make('credits_amount')
                            ->label(fn (Get $get): string => $get('operation') === 'remove'
                                ? 'Créditos a quitar'
                                : 'Créditos a asignar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(999)
                            ->required()
                            ->default(1)
                            ->rule(function (Get $get) use ($record): \Closure {
                                return function (string $attribute, mixed $value, \Closure $fail) use ($get, $record): void {
                                    if ($get('operation') !== 'remove') {
                                        return;
                                    }

                                    $amount = (int) $value;
                                    $tenantIds = array_map('intval', $get('tenant_ids') ?? []);

                                    foreach ($tenantIds as $tenantId) {
                                        if ($record->activeCredits($tenantId) < $amount) {
                                            $fail('El usuario no tiene suficientes créditos en una o más sucursales seleccionadas.');

                                            return;
                                        }
                                    }
                                };
                            }),
                        TextInput::make('admin_password')
                            ->label('Tu contraseña de administrador')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(function (): \Closure {
                                return function (string $attribute, mixed $value, \Closure $fail): void {
                                    if (! is_string($value) || ! Hash::check($value, (string) auth()->user()?->password)) {
                                        $fail('La contraseña no coincide con tu cuenta de administrador.');
                                    }
                                };
                            }),
                    ])
                    ->action(function (User $record, array $data): void {
                        $amount = (int) $data['credits_amount'];
                        $tenantIds = array_map('intval', $data['tenant_ids'] ?? []);
                        $isRemoval = ($data['operation'] ?? 'assign') === 'remove';

                        try {
                            DB::transaction(function () use ($record, $amount, $tenantIds, $isRemoval): void {
                                foreach ($tenantIds as $tenantId) {
                                    if ($isRemoval) {
                                        $record->revokeAdminCredits($tenantId, $amount);
                                    } else {
                                        $record->grantAdminCredits($tenantId, $amount);
                                    }
                                }
                            });
                        } catch (\InvalidArgumentException $exception) {
                            Notification::make()
                                ->title('No se pudieron quitar los créditos')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        if (! $isRemoval) {
                            $record->fresh()->sendCreditsAssignedNotification('admin_manual', $tenantIds);
                        }

                        $branchCount = count($tenantIds);
                        $branchLabel = $branchCount === 1 ? '1 sucursal' : "{$branchCount} sucursales";
                        $verb = $isRemoval ? 'quitaron' : 'acreditaron';

                        Notification::make()
                            ->title($isRemoval ? 'Créditos removidos' : 'Créditos asignados')
                            ->body("Se {$verb} {$amount} crédito(s) en {$branchLabel} para {$record->name}.")
                            ->success()
                            ->send();
                    }),
                Action::make('resendCredentials')
                    ->label('Reenviar credenciales')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reenviar credenciales de acceso')
                    ->modalDescription('Se generará una nueva contraseña temporal, se reemplazará la actual y se enviará al correo del usuario.')
                    ->action(function (User $record): void {
                        $plainPassword = Str::password(8, true, true, true, false);

                        DB::transaction(function () use ($record, $plainPassword): void {
                            $record->forceFill([
                                'password' => Hash::make($plainPassword),
                            ])->save();

                            Mail::to($record->email)->send(new WelcomeUserMail($record->fresh(), $plainPassword, true));
                        });

                        Notification::make()
                            ->title('Credenciales reenviadas')
                            ->body('Se generó una nueva contraseña y se envió al correo del usuario.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function branchCreditLabels(User $user): array
    {
        $balances = $user->credits
            ->groupBy('tenant_id')
            ->map(fn ($credits) => $credits->sum('balance'));

        return self::orderedTenants()
            ->map(fn (Tenant $tenant): string => $tenant->name.': '.($balances[$tenant->id] ?? 0))
            ->all();
    }

    private static function branchCreditBadgeColor(string $state): string
    {
        $balance = (int) trim(explode(':', $state, 2)[1] ?? '0');

        return $balance > 0 ? 'success' : 'gray';
    }

    /** @return \Illuminate\Support\Collection<int, Tenant> */
    private static function orderedTenants()
    {
        static $tenants = null;

        $tenants ??= Tenant::query()->orderBy('name')->get(['id', 'name']);

        return $tenants;
    }

    private static function branchBalancesHtml(User $user): string
    {
        $rows = self::orderedTenants()
            ->map(function (Tenant $tenant) use ($user): string {
                $balance = $user->activeCredits($tenant->id);

                return '<tr class="border-b border-gray-200 dark:border-gray-700">'
                    .'<td class="py-2 pr-4 font-medium">'.e($tenant->name).'</td>'
                    .'<td class="py-2 text-right">'
                    .($balance > 0
                        ? '<span class="font-bold text-success-600">'.$balance.'</span>'
                        : '<span class="text-gray-400">0</span>')
                    .'</td></tr>';
            })
            ->implode('');

        return '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">'
            .'<table class="w-full text-sm"><thead><tr class="bg-gray-50 dark:bg-gray-800">'
            .'<th class="px-3 py-2 text-left font-semibold">Sucursal</th>'
            .'<th class="px-3 py-2 text-right font-semibold">Activos</th>'
            .'</tr></thead><tbody class="px-3">'.$rows.'</tbody></table></div>';
    }
}
