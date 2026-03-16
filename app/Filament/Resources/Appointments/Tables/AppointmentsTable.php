<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use App\Models\UserCredit;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                        if (!in_array($record->check_in_status, ['pendiente', 'cobrar_al_llegar'])) {
                            return false;
                        }

                        // Calcular diferencia en minutos. (false permite números negativos si ya pasó la hora)
                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);

                        // Visible desde 20 minutos antes de la clase en adelante
                        return $diffInMinutes <= 20;
                    })
                    ->form(function (Appointment $record) {
                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);

                        // Si es menor a -30, significa que ya pasaron 30 minutos o más
                        $isLate = $diffInMinutes <= -30;
                        $requiresPayment = $record->check_in_status === 'cobrar_al_llegar';

                        return [
                            // Mensaje de alerta si requiere cobro
                            Placeholder::make('alerta_cobro')
                                ->hidden(! $requiresPayment)
                                ->content(new \Illuminate\Support\HtmlString('<span class="text-red-600 font-bold">¡ATENCIÓN! Esta cita se agendó sin créditos. Debes cobrar la sesión (efectivo/terminal) antes de darle acceso al cliente.</span>')),

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
                                ->accepted()
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
                    ->modalDescription(function (Appointment $record) {
                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        $minutesDiff = now()->diffInMinutes($appointmentTime, false); // false para que de negativos si ya pasó

                        if ($minutesDiff >= 120) {
                            return 'Faltan 2 horas o más para esta clase. Al cancelar, se DEVOLVERÁ 1 crédito al cliente automáticamente. (⚠️ Solo Administradores).';
                        }
                        return 'Faltan menos de 2 horas (o la cita ya pasó). Se cancelará el espacio pero NO se devolverá el crédito al cliente.';
                    })
                    ->action(function (Appointment $record) {
                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        $minutesDiff = now()->diffInMinutes($appointmentTime, false);

                        // ESCENARIO 1: Mayor a 2 horas (120 minutos)
                        if ($minutesDiff >= 120) {
                            if (!auth()->user()->hasRole('admin')) {
                                Notification::make()
                                    ->title('Permiso Denegado')
                                    ->body('Solo un Administrador puede cancelar con más de 2 horas de anticipación y devolver créditos.')
                                    ->danger()
                                    ->send();
                                return; // Detiene la ejecución
                            }

                            // 1. Cancelar Cita
                            $record->update(['status' => 'cancelled', 'check_in_status' => 'cancelada_por_administrador', 'checked_in_by' => auth()->id()]);

                            // 2. Buscar un paquete de créditos activo para devolverle el saldo
                            $activeCredit = UserCredit::where('user_id', $record->user_id)
                                ->where('tenant_id', $record->tenant_id)
                                ->where('expires_at', '>', now())
                                ->orderBy('expires_at', 'desc')
                                ->first();

                            if ($activeCredit) {
                                $activeCredit->increment('balance', 1);
                            } else {
                                // Si no tenía paquetes activos, le creamos un crédito de cortesía por 30 días
                                UserCredit::create([
                                    'user_id' => $record->user_id,
                                    'tenant_id' => $record->tenant_id,
                                    'balance' => 1,
                                    'expires_at' => now()->addDays(30),
                                    'is_special' => false,
                                ]);
                            }

                            Notification::make()
                                ->title('Cancelación Exitosa')
                                ->body('El espacio ha sido liberado y se devolvió 1 crédito al cliente.')
                                ->success()
                                ->send();
                        } else {
                            $record->update(['status' => 'cancelled', 'check_in_status' => 'cancelada_por_empleado', 'checked_in_by' => auth()->id()]);

                            Notification::make()
                                ->title('Cancelación Tardía')
                                ->body('El espacio ha sido liberado. NO se devolvió el crédito al cliente según la política de 2 horas.')
                                ->warning()
                                ->send();
                        }
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
                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        if ($appointmentTime->isPast()) {
                            return false;
                        }

                        return true;
                    }),
            ]);
    }
}
