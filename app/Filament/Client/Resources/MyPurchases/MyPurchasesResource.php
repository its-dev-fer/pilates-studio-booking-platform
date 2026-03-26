<?php

namespace App\Filament\Client\Resources\MyPurchases;

use App\Filament\Client\Resources\MyPurchases\Pages\ListMyPurchases;
use App\Filament\Client\Resources\MyPurchases\Pages\ViewMyPurchase;
use App\Filament\Client\Resources\MyPurchases\Schemas\MyPurchaseInfolist;
use App\Filament\Client\Resources\MyPurchases\Tables\MyPurchasesTable;
use App\Models\Order;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MyPurchasesResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'mis-compras';

    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-bag';
    }

    public static function getModelLabel(): string
    {
        return 'Compra';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mis compras';
    }

    public static function getNavigationLabel(): string
    {
        return 'Mis compras';
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return (int) $record->user_id === (int) auth()->id();
    }

    /**
     * Solo pedidos del usuario en la sucursal (tenant) actual del panel.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['tenant'])
            ->orderByDesc('created_at');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return MyPurchaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyPurchasesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyPurchases::route('/'),
            'view' => ViewMyPurchase::route('/{record}'),
        ];
    }
}
