<?php

use App\Http\Controllers\CheckoutController;
use App\Livewire\Checkout;
use App\Livewire\CreditPackages;
use App\Livewire\GuestCreditPackages;
use App\Livewire\LandingPage;
use App\Livewire\ProductShow;
use App\Livewire\ShoppingCart;
use App\Livewire\StoreFront;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('home');
Route::get('/paquetes-disponibles', GuestCreditPackages::class)->name('credits.guest');

Route::middleware(['auth'])->group(function () {
    // Vista de paquetes
    Route::get('/comprar-creditos', CreditPackages::class)->name('checkout.credits');

    // Procesamiento y Webhooks de Stripe Cashier
    Route::get('/checkout/procesar/{package}', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

Route::get('/login', function () {
    return redirect('/clientes/login');
})->name('login');

Route::get('/tienda', StoreFront::class)->name('store.index');
Route::get('/tienda/producto/{slug}', ProductShow::class)->name('store.product.show');
Route::get('/carrito', ShoppingCart::class)->name('store.cart');
Route::get('/checkout', Checkout::class)->name('store.checkout');
