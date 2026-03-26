<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);
    }
}
