<?php

namespace App\Filament\Resources\CreditPackages\Pages;

use App\Filament\Resources\CreditPackages\CreditPackageResource;
use App\Services\StripeCreditPackageProductService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Throwable;

class CreateCreditPackage extends CreateRecord
{
    protected static string $resource = CreditPackageResource::class;

    protected function afterCreate(): void
    {
        try {
            app(StripeCreditPackageProductService::class)->syncBasePriceWithStripe($this->record);
            $this->record->refresh();

            Notification::make()
                ->title('Stripe sincronizado')
                ->body('Producto y precio base creados en Stripe.')
                ->success()
                ->send();
        } catch (Throwable $e) {
            report($e);

            Notification::make()
                ->title('No se pudo sincronizar con Stripe')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
