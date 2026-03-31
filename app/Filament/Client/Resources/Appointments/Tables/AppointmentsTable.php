<?php

namespace App\Filament\Client\Resources\Appointments\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([
            ]);
    }
}
