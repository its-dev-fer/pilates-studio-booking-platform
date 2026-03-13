<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CreditPackage;

class CreditPackages extends Component
{
    public $packages;
    public $activeCredits = 0;

    public function mount()
    {
        $this->packages = CreditPackage::all();

        $this->activeCredits = auth()->user()->credits()
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');
    }

    public function render()
    {
        return view('livewire.credit-packages')->layout('layouts.landing');
    }
}
