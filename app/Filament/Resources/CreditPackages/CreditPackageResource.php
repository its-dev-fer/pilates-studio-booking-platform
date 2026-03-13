<?php

namespace App\Filament\Resources\CreditPackages;

use App\Filament\Resources\CreditPackages\Pages\CreateCreditPackage;
use App\Filament\Resources\CreditPackages\Pages\EditCreditPackage;
use App\Filament\Resources\CreditPackages\Pages\ListCreditPackages;
use App\Filament\Resources\CreditPackages\Schemas\CreditPackageForm;
use App\Filament\Resources\CreditPackages\Tables\CreditPackagesTable;
use App\Models\CreditPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CreditPackageResource extends Resource
{
    protected static ?string $model = CreditPackage::class;

    protected static ?string $modelLabel = 'Paquete de Créditos';
    protected static ?string $pluralModelLabel = 'Paquetes de Créditos';

    protected static bool $isScopedToTenant = false;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Ventas y Créditos';
    }

    protected static ?string $recordTitleAttribute = 'credits';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }

    public static function form(Schema $schema): Schema
    {
        return CreditPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditPackagesTable::configure($table);
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
            'index' => ListCreditPackages::route('/'),
            'create' => CreateCreditPackage::route('/create'),
            'edit' => EditCreditPackage::route('/{record}/edit'),
        ];
    }
}
