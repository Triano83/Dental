<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AlbaranController;
use App\Http\Controllers\FacturaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rutas para la gestión de clientes (CRUD completo)
Route::resource('clientes', ClienteController::class);

// Rutas para la gestión de productos (CRUD completo)
Route::resource('productos', ProductoController::class);

// Rutas PERSONALIZADAS para la gestión de albaranes
// Se usan rutas individuales para controlar el nombre del parámetro como {albaran}
// Esto es importante para el Route Model Binding y para que los helpers route() funcionen
Route::get('albaranes', [AlbaranController::class, 'index'])->name('albaranes.index');
Route::get('albaranes/create', [AlbaranController::class, 'create'])->name('albaranes.create');
Route::post('albaranes', [AlbaranController::class, 'store'])->name('albaranes.store');
Route::get('albaranes/{albaran}', [AlbaranController::class, 'show'])->name('albaranes.show');
Route::get('albaranes/{albaran}/edit', [AlbaranController::class, 'edit'])->name('albaranes.edit');
Route::put('albaranes/{albaran}', [AlbaranController::class, 'update'])->name('albaranes.update');
Route::delete('albaranes/{albaran}', [AlbaranController::class, 'destroy'])->name('albaranes.destroy');


// Rutas para la gestión de facturas (CRUD y generación)
// ¡IMPORTANTE: Definir las rutas específicas (como generar-form) ANTES del Route::resource genérico!
Route::get('facturas/generar-form', [FacturaController::class, 'showGenerateForm'])->name('facturas.generar.form');
Route::post('facturas/generar', [FacturaController::class, 'generarFacturas'])->name('facturas.generar');
Route::resource('facturas', FacturaController::class); // Definido al final para evitar conflictos de URL