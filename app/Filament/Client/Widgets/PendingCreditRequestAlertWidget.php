<?php

namespace App\Filament\Client\Widgets;

use App\Models\CreditPurchaseRequest;
use App\Models\User;
use Filament\Facades\Filament;
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
            ->with(['package', 'requestedTenant'])
            ->where('user_id', Auth::id())
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->latest()
            ->first();

        if (
            $this->pendingRequest
            && $this->pendingRequest->payment_method === CreditPurchaseRequest::METHOD_TRANSFER
            && ! $this->pendingRequest->requestedTenant
        ) {
            $tenant = Filament::getTenant();

            if ($tenant) {
                $this->pendingRequest->setRelation('requestedTenant', $tenant);
            }
        }
    }

    public function getFormattedTransferAccountNumberProperty(): string
    {
        $accountNumber = $this->pendingRequest?->requestedTenant?->transfer_account_number;

        if (! is_string($accountNumber) || $accountNumber === '') {
            return 'No disponible para esta sucursal';
        }

        $digits = preg_replace('/\D+/', '', $accountNumber) ?? '';
        $length = strlen($digits);

        if ($length === 16) {
            return trim(chunk_split($digits, 4, ' '));
        }

        if ($length === 18) {
            return trim(chunk_split($digits, 3, ' '));
        }

        return $accountNumber;
    }
}
