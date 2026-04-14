<?php

namespace App\Filament\Resources\CreditPackages\Pages;

use App\Filament\Resources\CreditPackages\CreditPackageResource;
use App\Services\StripeCreditPackageProductService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditCreditPackage extends EditRecord
{
    protected static string $resource = CreditPackageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->getActivePromotion()) {
            $data['price'] = $this->record->price;
            $data['has_new_customer_price'] = $this->record->has_new_customer_price;
            $data['new_customer_price'] = $this->record->new_customer_price;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        try {
            app(StripeCreditPackageProductService::class)->syncBasePriceWithStripe($this->record);
            $this->record->refresh();

            Notification::make()
                ->title('Stripe actualizado')
                ->body('Producto y precio base revisados en Stripe.')
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
