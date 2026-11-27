<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'catalogo')->name('catalogo');
Route::view('/recorrido', 'recorrido')->name('recorrido');