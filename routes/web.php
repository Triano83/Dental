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
// Route::resource('albaranes', AlbaranController::class); // Comentar o eliminar esta línea

// Listar albaranes
Route::get('albaranes', [AlbaranController::class, 'index'])->name('albaranes.index');
// Mostrar formulario de creación
Route::get('albaranes/create', [AlbaranController::class, 'create'])->name('albaranes.create');
// Almacenar nuevo albarán
Route::post('albaranes', [AlbaranController::class, 'store'])->name('albaranes.store');
// Mostrar detalles de un albarán específico
Route::get('albaranes/{albaran_id}', [AlbaranController::class, 'show'])->name('albaranes.show');
// Mostrar formulario de edición
Route::get('albaranes/{albaran_id}/edit', [AlbaranController::class, 'edit'])->name('albaranes.edit');
// Actualizar albarán
Route::put('albaranes/{albaran_id}', [AlbaranController::class, 'update'])->name('albaranes.update');
// Eliminar albarán
Route::delete('albaranes/{albaran_id}', [AlbaranController::class, 'destroy'])->name('albaranes.destroy');


// Rutas para la gestión de facturas (CRUD y generación)
Route::resource('facturas', FacturaController::class);
Route::get('facturas/generar-form', [FacturaController::class, 'showGenerateForm'])->name('facturas.generar.form');
Route::post('facturas/generar', [FacturaController::class, 'generarFacturas'])->name('facturas.generar');