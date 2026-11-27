<?php
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::view('/', 'catalogo')->name('catalogo');
Route::view('/recorrido', 'recorrido')->name('recorrido');
Route::view('/carrito', 'carrito')->name('carrito');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/exito', [CheckoutController::class, 'exito'])->name('checkout.exito');
Route::get('/checkout/cancelado', [CheckoutController::class, 'cancelado'])->name('checkout.cancelado');

Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('login');
Route::post('/admin/login', [AdminController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.store');

Route::middleware('auth')->group(function () {
    Route::get('/admin/pedidos', [AdminController::class, 'pedidos'])->name('admin.pedidos');
    Route::post('/admin/pedidos/{order}/estado', [AdminController::class, 'estado'])->name('admin.estado');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});