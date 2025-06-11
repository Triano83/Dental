@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalles del Producto: {{ $producto->nombre }}</h2>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-header">
            Información del Producto
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
            <p><strong>Precio:</strong> {{ number_format($producto->precio, 2, ',', '.') }} €</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-primary">Editar Producto</a>
        </div>
    </div>
@endsection