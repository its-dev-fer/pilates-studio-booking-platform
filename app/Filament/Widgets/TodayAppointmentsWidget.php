<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayAppointmentsWidget extends TableWidget
{
    protected static ?string $heading = '📅 Clases y Citas de Hoy';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    // REGLA: Solo visible para Admin y Empleado
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole(['admin', 'empleado']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->where('tenant_id', Filament::getTenant()->id)
                    ->whereDate('date', today())
                    ->orderBy('time_slot', 'asc')
            )
            ->columns([
                TextColumn::make('time_slot')
                    ->label('Hora')
                    ->time('h:i A')
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estatus Cita')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('check_in_status')
                    ->label('Asistencia')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'cliente_llego' => 'success',
                        'cobrar_al_llegar' => 'danger',
                        default => 'warning',
                    })
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                // BOTÓN DE CHECK-IN (El mismo que hicimos en el Resource)
                Action::make('check_in')
                    ->label('Check-In')
                    ->icon('heroicon-o-check-badge')
                    ->button() // Lo hacemos ver como un botón sólido para que destaque
                    ->color('success')
                    ->visible(function (Appointment $record) {
                        if (!in_array($record->check_in_status, ['pendiente', 'cobrar_al_llegar'])) {
                            return false;
                        }

                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);

                        return $diffInMinutes <= 20; 
                    })
                    ->form(function (Appointment $record) {
                        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
                        $diffInMinutes = now()->diffInMinutes($appointmentTime, false);
                        $isLate = $diffInMinutes <= -30;
                        $requiresPayment = $record->check_in_status === 'cobrar_al_llegar';

                        return [
                            Placeholder::make('alerta_cobro')
                                ->hidden(! $requiresPayment)
                                ->content(new \Illuminate\Support\HtmlString('<span class="text-red-600 font-bold">¡ATENCIÓN! Esta cita se agendó sin créditos. Debes cobrar la sesión (efectivo/terminal) antes de darle acceso al cliente.</span>')),

                            Select::make('new_check_in_status')
                                ->label('Estado de Asistencia')
                                ->options(function () use ($isLate) {
                                    if ($isLate) {
                                        return [
                                            'cliente_no_llego' => 'Cliente No Llegó',
                                            'cancelada_por_empleado' => 'Cancelada por Empleado (Tardía)',
                                        ];
                                    }
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
                                ->required(fn () => $requiresPayment)
                                ->accepted()
                        ];
                    })
                    ->action(function (Appointment $record, array $data) {
                        $record->update([
                            'check_in_status' => $data['new_check_in_status'],
                            'checked_in_by' => auth()->id(),
                            'status' => $data['new_check_in_status'] === 'cliente_llego' ? 'completed' : $record->status,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Check-in Registrado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->emptyStateHeading('No hay citas para hoy');
    }
}
