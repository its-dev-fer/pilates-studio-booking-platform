<?php

namespace App\Livewire;

use App\Models\CreditPackage;
use App\Support\CreditPackagePromotionPricing;
use Livewire\Component;

class GuestCreditPackages extends Component
{
    public $packages;

    /** @var array<int, array{base_price: float, final_price: float, promotion: mixed, applied_label: ?string, has_new_customer_price: bool}> */
    public array $pricingByPackageId = [];

    public function mount(): void
    {
        $this->packages = CreditPackage::query()->get();

        $this->pricingByPackageId = [];
        foreach ($this->packages as $package) {
            $this->pricingByPackageId[$package->id] = CreditPackagePromotionPricing::resolve($package, now(), null);
        }
    }

    public function render()
    {
        return view('livewire.guest-credit-packages')->layout('layouts.app');
    }
}
