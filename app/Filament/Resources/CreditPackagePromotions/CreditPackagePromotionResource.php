<?php

namespace App\Filament\Resources\CreditPackagePromotions;

use App\Filament\Resources\CreditPackagePromotions\Pages\CreateCreditPackagePromotion;
use App\Filament\Resources\CreditPackagePromotions\Pages\EditCreditPackagePromotion;
use App\Filament\Resources\CreditPackagePromotions\Pages\ListCreditPackagePromotions;
use App\Filament\Resources\CreditPackagePromotions\Pages\ViewCreditPackagePromotion;
use App\Filament\Resources\CreditPackagePromotions\Schemas\CreditPackagePromotionForm;
use App\Filament\Resources\CreditPackagePromotions\Schemas\CreditPackagePromotionInfolist;
use App\Filament\Resources\CreditPackagePromotions\Tables\CreditPackagePromotionsTable;
use App\Models\CreditPackagePromotion;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreditPackagePromotionResource extends Resource
{
    protected static ?string $model = CreditPackagePromotion::class;

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 12;

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): ?string
    {
        return 'Ventas y Créditos';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-sparkles';
    }

    public static function getModelLabel(): string
    {
        return 'Promoción de paquete';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Promociones de paquetes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Promociones (créditos)';
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record instanceof CreditPackagePromotion) {
            return null;
        }

        $record->loadMissing('package');

        return sprintf(
            '%s — %s → %s',
            $record->package?->name ?? 'Paquete',
            $record->starts_at->format('d/m/Y H:i'),
            $record->ends_at->format('d/m/Y H:i'),
        );
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['package']);
    }

    public static function form(Schema $schema): Schema
    {
        return CreditPackagePromotionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CreditPackagePromotionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditPackagePromotionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCreditPackagePromotions::route('/'),
            'create' => CreateCreditPackagePromotion::route('/create'),
            'view' => ViewCreditPackagePromotion::route('/{record}'),
            'edit' => EditCreditPackagePromotion::route('/{record}/edit'),
        ];
    }
}
