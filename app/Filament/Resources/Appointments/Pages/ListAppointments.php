<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function getTabs(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        return [
            'this_week' => Tab::make('Esta semana')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereBetween('date', [$startOfWeek, $endOfWeek])),
            'past' => Tab::make('Citas pasadas')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('date', '<', $startOfWeek)),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'this_week';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
