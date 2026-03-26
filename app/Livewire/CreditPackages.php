<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CreditPackage;
use Illuminate\Support\Facades\Auth;

class CreditPackages extends Component
{
    public $packages;
    public $activeCredits = 0;

    public function mount()
    {
        $user = Auth::user();

        $this->packages = CreditPackage::query()
            ->where(function ($query) use ($user) {
                $query->where('is_one_time_purchase', false)
                    ->orWhereDoesntHave('purchases', function ($purchaseQuery) use ($user) {
                        $purchaseQuery->where('user_id', $user->id);
                    });
            })
            ->get();

        $this->activeCredits = $user->credits()
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');
    }

    public function render()
    {
        return view('livewire.credit-packages')->layout('layouts.app');
    }
}
