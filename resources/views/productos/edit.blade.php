@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Editar Producto</h2>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <form action="{{ route('productos.update', $producto->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Usa el método PUT para la actualización --}}

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto:</label>
            <input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $producto->nombre) }}">
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" step="0.01" name="precio" class="form-control" required value="{{ old('precio', $producto->precio) }}">
        </div>
        <button type="submit" class="btn btn-success">Actualizar Producto</button>
    </form>
@endsection