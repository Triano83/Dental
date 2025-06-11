<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AlbaranController; // Añade esto
use App\Http\Controllers\FacturaController; // Añade esto




Route::get('/', function () {
    return view('welcome'); // Puedes cambiar esto por tu dashboard principal más adelante
});

// Rutas para la gestión de clientes (CRUD completo)
Route::resource('clientes', ClienteController::class);

// Rutas para la gestión de productos (CRUD completo)
Route::resource('productos', ProductoController::class);

// Rutas para la gestión de albaranes (CRUD completo)
Route::resource('albaranes', AlbaranController::class);

// Rutas para la gestión de facturas (CRUD y generación)
Route::resource('facturas', FacturaController::class);
Route::get('facturas/generar-form', [FacturaController::class, 'showGenerateForm'])->name('facturas.generar.form');
Route::post('facturas/generar', [FacturaController::class, 'generarFacturas'])->name('facturas.generar');