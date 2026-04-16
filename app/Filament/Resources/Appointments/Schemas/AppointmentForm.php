<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Appointment;
use App\Models\CreditPurchaseRequest;
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
                        ->label('Horario de Clase')
                        ->required()
                        ->searchable()
                        ->allowHtml() // Permitimos inyectar colores en las opciones
                        ->helperText('Selecciona la fecha primero. Se muestran los lugares disponibles.')
                        ->options(function (callable $get, ?Appointment $record) {
                            $dateStr = $get('date');
                            if (!$dateStr) return [];

                            $tenant = Filament::getTenant();
                            $date = \Carbon\Carbon::parse($dateStr);
                            $capacity = $tenant->capacity_per_slot ?? 5; // Límite por clase

                            $dayOfWeek = $date->dayOfWeekIso;
                            $businessHours = collect($tenant->business_hours ?? [])->firstWhere('day', $dayOfWeek);

                            if (!$businessHours || empty($businessHours['slots'])) return []; // No hay clases este día

                            // Buscar TODAS las citas del día para contar cuántas hay en cada horario
                            $query = Appointment::where('tenant_id', $tenant->id)
                                ->whereDate('date', $date->format('Y-m-d'))
                                ->where('status', 'scheduled');
                                
                            if ($record) {
                                $query->where('id', '!=', $record->id); // Ignorar la propia cita al editar
                            }
                            
                            $appointments = $query->get();
                            $pendingRequests = CreditPurchaseRequest::query()
                                ->where('requested_tenant_id', $tenant->id)
                                ->whereDate('requested_date', $date->format('Y-m-d'))
                                ->where('status', CreditPurchaseRequest::STATUS_PENDING)
                                ->whereIn('payment_method', [CreditPurchaseRequest::METHOD_TRANSFER, CreditPurchaseRequest::METHOD_CASH])
                                ->whereNotNull('requested_time_slot')
                                ->get();
                            $availableOptions = [];

                            // Iteramos sobre los bloques exactos que definió el admin (ej. 06:00, 07:00, 09:00)
                            foreach ($businessHours['slots'] as $timeString) {
                                $slotTime = \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $timeString);
                                
                                $isCurrentRecordSlot = $record 
                                    && $record->date->format('Y-m-d') === $date->format('Y-m-d') 
                                    && \Carbon\Carbon::parse($record->time_slot)->format('H:i') === $timeString;

                                // Omitir si la hora ya pasó (a menos que estemos editando ESA misma hora)
                                if (!$isCurrentRecordSlot && $slotTime->isPast()) {
                                    continue;
                                }

                                // Contar cuántas personas ya reservaron esta hora
                                $bookedCount = $appointments->filter(function ($app) use ($timeString) {
                                    return \Carbon\Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                                })->count();
                                $heldByPendingRequests = $pendingRequests->filter(function (CreditPurchaseRequest $request) use ($timeString) {
                                    return \Carbon\Carbon::parse((string) $request->requested_time_slot)->format('H:i') === $timeString;
                                })->count();

                                $availableSpots = $capacity - ($bookedCount + $heldByPendingRequests);

                                // Si hay lugares disponibles (o es la cita actual), lo agregamos con colores
                                if ($availableSpots > 0 || $isCurrentRecordSlot) {
                                    // Lógica de Semáforo
                                    if ($availableSpots >= 3) {
                                        $color = 'text-emerald-600'; // Verde
                                        $status = 'Disponible';
                                    } elseif ($availableSpots == 2) {
                                        $color = 'text-amber-500'; // Amarillo
                                        $status = 'Casi lleno';
                                    } else {
                                        $color = 'text-orange-600'; // Naranja
                                        $status = 'Último lugar';
                                    }

                                    $formattedTime = date('h:i A', strtotime($timeString));
                                    
                                    // Diseño HTML para la opción del Select
                                    $html = "<div class='flex justify-between items-center w-full'>
                                                <span class='font-medium'>{$formattedTime}</span>
                                                <span class='text-sm font-bold {$color}'>{$availableSpots} lugares ({$status})</span>
                                             </div>";
                                             
                                    $availableOptions[$timeString] = $html;
                                }
                            }

                            return $availableOptions;
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
