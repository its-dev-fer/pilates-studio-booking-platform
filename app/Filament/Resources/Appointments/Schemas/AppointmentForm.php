<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Appointment;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Reserva')->schema([
                    Select::make('user_id')
                        ->label('Cliente')
                        ->relationship('user', 'name', fn (Builder $query) => $query->role('cliente'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Si el cliente no tiene créditos, se te notificará para cobrarle al llegar.'),

                    DatePicker::make('date')
                        ->label('Fecha')
                        ->required()
                        ->minDate(fn (string $operation) => $operation === 'create' ? today() : null)
                        ->maxDate(fn (string $operation) => $operation === 'create' ? today()->endOfMonth() : null)
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('time_slot', null)),// Resetea la hora si cambian la fecha

                    Select::make('time_slot')
                        ->label('Horario Disponible')
                        ->required()
                        ->searchable()
                        ->helperText('Selecciona la fecha primero. Solo se muestran horarios disponibles.')
                        ->options(function (callable $get, ?Appointment $record) {
                            $dateStr = $get('date');
                            if (!$dateStr) {
                                return [];
                            }

                            $tenant = Filament::getTenant();
                            $date = \Carbon\Carbon::parse($dateStr);
                            $today = now();

                            $query = Appointment::where('tenant_id', $tenant->id)
                                ->whereDate('date', $date->format('Y-m-d'))
                                ->where('status', 'scheduled');

                            if ($record) {
                                $query->where('id', '!=', $record->id);
                            }

                            $appointments = $query->get();
                            if ($appointments->count() >= $tenant->max_appointments_per_day) {
                                return []; // Capacidad máxima alcanzada
                            }

                            // 2. Extraer el horario operativo del día seleccionado
                            $dayOfWeek = $date->dayOfWeekIso;
                            $businessHours = collect($tenant->business_hours ?? [])->firstWhere('day', $dayOfWeek);

                            if (!$businessHours) {
                                return [];
                            } // Día cerrado

                            $openTime = \Carbon\Carbon::parse($businessHours['open']);
                            $closeTime = \Carbon\Carbon::parse($businessHours['close']);
                            $slots = [];
                            $currentSlot = $openTime->copy();

                            // 3. Generar bloques de 1 hora
                            while ($currentSlot->lt($closeTime)) {
                                $timeString = $currentSlot->format('H:i');

                                $isCurrentRecordSlot = $record
                                    && $record->date->format('Y-m-d') === $date->format('Y-m-d')
                                    && \Carbon\Carbon::parse($record->time_slot)->format('H:i') === $timeString;

                                // Omitir horas pasadas (PERO perdonar la hora original si estamos editando)
                                if (!$isCurrentRecordSlot && $date->isToday() && $currentSlot->copy()->setDate($date->year, $date->month, $date->day)->isPast()) {
                                    $currentSlot->addHour();
                                    continue;
                                }

                                // Omitir si ya está reservado ese bloque exacto
                                $isBooked = $appointments->contains(function ($app) use ($timeString) {
                                    return \Carbon\Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                                });

                                if (!$isBooked) {
                                    $slots[$timeString] = $timeString;
                                }

                                $currentSlot->addHour();
                            }

                            return $slots;
                        }),

                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'scheduled' => 'Agendada',
                            'completed' => 'Completada',
                            'cancelled' => 'Cancelada',
                        ])
                        ->default('scheduled')
                        ->disabled()
                        ->helperText('Para cancelar la cita, usa el boton de cancelar en la vista de todas las citas, no cambies el estado aquí.')
                        ->required(),
                    Select::make('check_in_status')
                        ->label('Check In')
                        ->disabled(! auth()->user()->hasRole('admin'))
                        ->options([
                            'pendiente' => 'Pendiente',
                            'cobrar_al_llegar' => 'Cobrar Al Llegar',
                        ])->default('pendiente')->required()->helperText('Si seleccionas "Cobrar al llegar", el cliente no podrá hacer check-in hasta que se cobre la clase. Solo usar en casos especiales.'),
                ])->columns(2),
            ]);
    }
}
