<?php

namespace App\Filament\Client\Resources\Appointments\Schemas;

use App\Models\Appointment;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agendar Nueva Clase')->schema([
                    DatePicker::make('date')
                        ->label('Fecha')
                        ->required()
                        ->minDate(now())
                        ->maxDate(now()->endOfMonth())
                        ->live() // Hace que el formulario reaccione al cambiar la fecha
                        ->afterStateUpdated(fn (callable $set) => $set('time_slot', null)), // Limpia la hora si cambia la fecha

                    Select::make('time_slot')
                        ->label('Horario Disponible')
                        ->required()
                        ->options(function (callable $get) {
                            $dateStr = $get('date');
                            if (!$dateStr) {
                                return [];
                            }

                            $tenant = Filament::getTenant();
                            $date = \Carbon\Carbon::parse($dateStr);
                            $today = now();

                            // Traer citas de ese día
                            $appointments = Appointment::where('tenant_id', $tenant->id)
                                ->whereDate('date', $date->format('Y-m-d'))
                                ->where('status', 'scheduled')
                                ->get();

                            if ($appointments->count() >= $tenant->max_appointments_per_day) {
                                return []; // Lleno
                            }

                            $dayOfWeek = $date->dayOfWeekIso;
                            $businessHours = collect($tenant->business_hours ?? [])->firstWhere('day', $dayOfWeek);

                            if (!$businessHours) {
                                return [];
                            } // Cerrado

                            $openTime = \Carbon\Carbon::parse($businessHours['open']);
                            $closeTime = \Carbon\Carbon::parse($businessHours['close']);
                            $slots = [];
                            $currentSlot = $openTime->copy();

                            while ($currentSlot->lt($closeTime)) {
                                $timeString = $currentSlot->format('H:i');

                                if ($date->isToday() && $currentSlot->copy()->setDate($date->year, $date->month, $date->day)->isPast()) {
                                    $currentSlot->addHour();
                                    continue;
                                }

                                $isBooked = $appointments->contains(function ($app) use ($timeString) {
                                    return \Carbon\Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                                });

                                if (!$isBooked) {
                                    $slots[$timeString] = $timeString; // Formato [value => label] para Select
                                }

                                $currentSlot->addHour();
                            }

                            return $slots;
                        })
                        ->searchable()
                        ->helperText('Selecciona primero la fecha para ver los horarios disponibles.'),

                    Hidden::make('user_id')
                        ->default(auth()->id()),
                ])->columns(2),
            ]);
    }
}
