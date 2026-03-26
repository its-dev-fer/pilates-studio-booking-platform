<?php

namespace App\Filament\Resources\StoreSections;

use App\Filament\Resources\StoreSections\Pages\CreateStoreSection;
use App\Filament\Resources\StoreSections\Pages\EditStoreSection;
use App\Filament\Resources\StoreSections\Pages\ListStoreSections;
use App\Filament\Resources\StoreSections\Schemas\StoreSectionForm;
use App\Filament\Resources\StoreSections\Tables\StoreSectionsTable;
use App\Models\StoreSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class StoreSectionResource extends Resource
{
    protected static ?string $model = StoreSection::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|UnitEnum|null $navigationGroup = 'E-commerce';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Sección de Tienda';

    protected static ?string $pluralModelLabel = 'Secciones de Tienda';

    protected static bool $isScopedToTenant = false;

    protected static ?string $recordTitleAttribute = 'Store Section';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['admin', 'empleado']);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return StoreSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoreSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoreSections::route('/'),
            'create' => CreateStoreSection::route('/create'),
            'edit' => EditStoreSection::route('/{record}/edit'),
        ];
    }
}
