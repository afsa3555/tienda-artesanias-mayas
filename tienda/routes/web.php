<?php
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'catalogo')->name('catalogo');
Route::view('/recorrido', 'recorrido')->name('recorrido');
Route::view('/carrito', 'carrito')->name('carrito');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/exito', [CheckoutController::class, 'exito'])->name('checkout.exito');
Route::get('/checkout/cancelado', [CheckoutController::class, 'cancelado'])->name('checkout.cancelado');