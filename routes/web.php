<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AlbaranController;
use App\Http\Controllers\FacturaController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para la gestión de clientes (CRUD completo)
Route::resource('clientes', ClienteController::class);

// Rutas para la gestión de productos (CRUD completo)
Route::resource('productos', ProductoController::class);

// Rutas para la gestión de albaranes (CRUD completo)
// Si bien estás usando un resource, lo tienes comentado y con rutas individuales.
// Lo ideal es que si quieres todas las rutas CRUD que proporciona el resource,
// descomentes la línea y uses el controlador con inyección implícita.
// Si quieres rutas personalizadas, el nombre del parámetro debe coincidir con el de la inyección implícita.

// Manteniendo tus rutas individuales, ajustamos el nombre del parámetro:
// Listar albaranes
Route::get('albaranes', [AlbaranController::class, 'index'])->name('albaranes.index');
// Mostrar formulario de creación
Route::get('albaranes/create', [AlbaranController::class, 'create'])->name('albaranes.create');
// Almacenar nuevo albarán
Route::post('albaranes', [AlbaranController::class, 'store'])->name('albaranes.store');
// Mostrar detalles de un albarán específico
Route::get('albaranes/{albaran}', [AlbaranController::class, 'show'])->name('albaranes.show'); // <--- CAMBIO AQUÍ
// Mostrar formulario de edición
Route::get('albaranes/{albaran}/edit', [AlbaranController::class, 'edit'])->name('albaranes.edit'); // <--- CAMBIO AQUÍ
// Actualizar albarán
Route::put('albaranes/{albaran}', [AlbaranController::class, 'update'])->name('albaranes.update'); // <--- CAMBIO AQUÍ
// Eliminar albarán
Route::delete('albaranes/{albaran}', [AlbaranController::class, 'destroy'])->name('albaranes.destroy'); // <--- CAMBIO AQUÍ


// Rutas para la gestión de facturas (CRUD y generación)
Route::resource('facturas', FacturaController::class);
Route::get('facturas/generar-form', [FacturaController::class, 'showGenerateForm'])->name('facturas.generar.form');
Route::post('facturas/generar', [FacturaController::class, 'generarFacturas'])->name('facturas.generar');