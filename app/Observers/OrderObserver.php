<?php

namespace App\Observers;

use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderObserver
{
    /**
     * @var array<string, string>
     */
    protected static array $previousStatusByKey = [];

    public function updating(Order $order): void
    {
        if (! $order->exists || ! $order->isDirty('status')) {
            return;
        }

        $key = (string) $order->getKey();
        self::$previousStatusByKey[$key] = (string) $order->getOriginal('status');
    }

    public function updated(Order $order): void
    {
        $key = (string) $order->getKey();

        if (! isset(self::$previousStatusByKey[$key])) {
            return;
        }

        $previousStatus = self::$previousStatusByKey[$key];
        unset(self::$previousStatusByKey[$key]);

        if ($previousStatus === $order->status) {
            return;
        }

        $order->load([
            'tenant',
            'user',
            'items' => fn ($query) => $query->with(['product' => fn ($q) => $q->withTrashed()]),
        ]);

        $recipient = $order->user?->email ?? $order->guest_email;

        if (! filled($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new OrderStatusUpdatedMail(
                order: $order,
                previousStatus: $previousStatus,
                newStatus: (string) $order->status,
                isGuestCheckout: $order->user_id === null,
            ));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
