<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('storeSection.name')
                    ->label('Sección Padre')
                    ->badge() // Lo muestra como una "píldora" visual
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL')
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
