<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use App\Models\UserCredit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('time_slot')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Agendada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    }),
                TextColumn::make('check_in_status')
                    ->label('Check-in')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'cliente_no_llego' => 'danger',
                        'cancelada_por_cliente' => 'danger',
                        'cancelada_por_administrador' => 'danger',
                        'cancelada_por_empleado' => 'danger',
                        'cobrar_al_llegar' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'cliente_no_llego' => 'El Cliente No llegó',
                        'cancelada_por_cliente' => 'Cancelada por cliente',
                        'cancelada_por_administrador' => 'Cancelada por Admin',
                        'cancelada_por_empleado' => 'Cancelada por empleado',
                        'cobrar_al_llegar' => 'Cobrar al llegar!',
                        default => $state,
                    }),
                TextColumn::make('checkedInBy.name')
                    ->label('Modificado por')
                    ->formatStateUsing(fn (string $state): string => $state ?? 'N/A'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'scheduled' => 'Agendadas',
                        'completed' => 'Completadas',
                        'cancelled' => 'Canceladas',
                    ]),
            ])
            ->actions([
                Action::make('check_in')
                    ->label('Check-In')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(function (Appointment $record) {
                        // Solo visible si está pendiente o requiere cobro
                        if (! in_array($record->check_in_status, ['pendiente', 'cobrar_al_llegar'])) {
                            return false;
                        }

                        // Calcular diferencia en minutos. (false permite números negativos si ya pasó la hora)
                        $appointmentTime = Carbon::parse($record->date->format('Y-m-d').' '.$record->time_slot);
                        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);

                        // Visible desde 20 minutos antes de la clase en adelante
                        return $diffInMinutes <= 20;
                    })
                    ->form(function (Appointment $record) {
                        $appointmentTime = Carbon::parse($record->date->format('Y-m-d').' '.$record->time_slot);
                        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);

                        // Si es menor a -30, significa que ya pasaron 30 minutos o más
                        $isLate = $diffInMinutes <= -30;
                        $requiresPayment = $record->check_in_status === 'cobrar_al_llegar';

                        return [
                            // Mensaje de alerta si requiere cobro
                            Placeholder::make('alerta_cobro')
                                ->hidden(! $requiresPayment)
                                ->content(new HtmlString('<span class="text-red-600 font-bold">¡ATENCIÓN! Esta cita se agendó sin créditos. Debes cobrar la sesión (efectivo/terminal) antes de darle acceso al cliente.</span>')),

                            Select::make('new_check_in_status')
                                ->label('Estado de Asistencia')
                                ->options(function () use ($isLate) {
                                    // Regla: Si pasaron 30 mins, ocultamos opciones positivas
                                    if ($isLate) {
                                        return [
                                            'cliente_no_llego' => 'Cliente No Llegó',
                                            'cancelada_por_empleado' => 'Cancelada por Empleado (Tardía)',
                                        ];
                                    }

                                    // Opciones normales dentro de tiempo (+/- 20 mins)
                                    return [
                                        'cliente_llego' => 'Cliente Llegó',
                                        'cliente_no_llego' => 'Cliente No Llegó',
                                        'cancelada_por_empleado' => 'Cancelada por Empleado',
                                    ];
                                })
                                ->default($isLate ? 'cliente_no_llego' : 'cliente_llego')
                                ->required(),

                            Checkbox::make('cobro_confirmado')
                                ->label('Confirmo que he cobrado el monto de esta clase.')
                                ->visible($requiresPayment)
                                ->required(fn () => $requiresPayment) // Es obligatorio marcarlo si es visible
                                ->accepted(),
                        ];
                    })
                    ->action(function (Appointment $record, array $data) {
                        // Al guardar, actualizamos el estatus general y el del check-in
                        $record->update([
                            'check_in_status' => $data['new_check_in_status'],
                            'checked_in_by' => auth()->id(),
                            // Si llegó, marcamos la cita como completada en el sistema general
                            'status' => $data['new_check_in_status'] === 'cliente_llego' ? 'completed' : $record->status,
                        ]);

                        Notification::make()
                            ->title('Check-in Registrado')
                            ->body('El control de asistencia se guardó correctamente.')
                            ->success()
                            ->send();
                    }),
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('tabler-calendar-cancel')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar cancelación')
                    ->visible(fn (Appointment $record) => $record->status === 'scheduled')
                    ->form([
                        Checkbox::make('return_credit')
                            ->label('Devolver 1 crédito al cliente')
                            ->helperText('Solo para administradores.')
                            ->visible(fn () => auth()->user()->hasRole('admin'))
                            ->default(function (Appointment $record): bool {
                                $appointmentTime = Carbon::parse($record->date->format('Y-m-d').' '.$record->time_slot);
                                $minutesDiff = now()->diffInMinutes($appointmentTime, false);

                                return $minutesDiff >= 360;
                            }),
                    ])
                    ->modalDescription(function (Appointment $record) {
                        $appointmentTime = Carbon::parse($record->date->format('Y-m-d').' '.$record->time_slot);
                        $minutesDiff = now()->diffInMinutes($appointmentTime, false); // false para que de negativos si ya pasó

                        if (auth()->user()->hasRole('admin')) {
                            if ($minutesDiff < -15) {
                                return 'Ya pasó la ventana de cancelación para administración (15 minutos después de la hora de la cita).';
                            }

                            return 'Como administrador, puedes decidir si se devuelve o no el crédito.';
                        }

                        if ($minutesDiff >= 360) {
                            return 'Faltan 6 horas o más para la cita. Al cancelar, se devolverá 1 crédito automáticamente.';
                        }

                        return 'Faltan menos de 6 horas (o ya inició). Se cancelará la cita sin devolución de crédito.';
                    })
                    ->action(function (Appointment $record, array $data) {
                        $appointmentTime = Carbon::parse($record->date->format('Y-m-d').' '.$record->time_slot);
                        $minutesDiff = now()->diffInMinutes($appointmentTime, false);
                        $isAdmin = auth()->user()->hasRole('admin');

                        if ($isAdmin && $minutesDiff < -15) {
                            Notification::make()
                                ->title('Cancelación fuera de tiempo')
                                ->body('Solo se puede cancelar hasta 15 minutos después de la hora de la cita.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'status' => 'cancelled',
                            'check_in_status' => $isAdmin ? 'cancelada_por_administrador' : 'cancelada_por_empleado',
                            'checked_in_by' => auth()->id(),
                        ]);

                        $shouldReturnCredit = $isAdmin
                            ? ((bool) ($data['return_credit'] ?? false))
                            : ($minutesDiff >= 360);

                        if ($shouldReturnCredit) {
                            $activeCredit = UserCredit::where('user_id', $record->user_id)
                                ->where('tenant_id', $record->tenant_id)
                                ->where('expires_at', '>', now())
                                ->orderBy('expires_at', 'desc')
                                ->first();

                            if ($activeCredit) {
                                $activeCredit->increment('balance', 1);
                            } else {
                                UserCredit::create([
                                    'user_id' => $record->user_id,
                                    'tenant_id' => $record->tenant_id,
                                    'balance' => 1,
                                    'expires_at' => now()->addDays(30),
                                    'is_special' => false,
                                ]);
                            }
                        } else {
                            // Sin devolución de crédito por política.
                        }

                        Notification::make()
                            ->title('Cancelación registrada')
                            ->body($shouldReturnCredit
                                ? 'La cita fue cancelada y se devolvió 1 crédito al cliente.'
                                : 'La cita fue cancelada sin devolución de crédito.')
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->visible(function (Appointment $record) {
                        $user = auth()->user();

                        // El admin siempre ve el botón
                        if ($user->hasRole('admin')) {
                            return true;
                        }

                        // Si el cliente ya llegó o se completó, lo ocultamos al empleado
                        if ($record->check_in_status === 'cliente_llego' || $record->status === 'completed') {
                            return false;
                        }

                        // Si la cita ya pasó, lo ocultamos al empleado
                        $appointmentTime = Carbon::parse($record->date->format('Y-m-d').' '.$record->time_slot);
                        if ($appointmentTime->isPast()) {
                            return false;
                        }

                        return true;
                    }),
            ]);
    }
}
