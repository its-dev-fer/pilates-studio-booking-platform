<?php

namespace App\Filament\Client\Widgets;

use App\Models\CreditPurchaseRequest;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PendingCreditRequestAlertWidget extends Widget
{
    protected string $view = 'filament.client.widgets.pending-credit-request-alert-widget';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public ?CreditPurchaseRequest $pendingRequest = null;

    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('cliente') ?? false;
    }

    public function mount(): void
    {
        $this->pendingRequest = CreditPurchaseRequest::query()
            ->with('package')
            ->where('user_id', Auth::id())
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->latest()
            ->first();
    }
}
