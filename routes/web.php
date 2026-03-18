<?php

use App\Http\Controllers\CheckoutController;
use App\Livewire\CreditPackages;
use Illuminate\Support\Facades\Route;
use App\Livewire\LandingPage;

Route::get('/', LandingPage::class)->name('home');

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
