@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Crear Nuevo Producto</h2>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto:</label>
            <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" step="0.01" name="precio" class="form-control" required value="{{ old('precio') }}">
        </div>
        <button type="submit" class="btn btn-success">Guardar Producto</button>
    </form>
@endsection