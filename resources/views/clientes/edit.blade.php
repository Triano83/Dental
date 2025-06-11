@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Editar Cliente</h2>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Usa el método PUT para la actualización --}}

        <div class="mb-3">
            <label for="nombre_clinica" class="form-label">Nombre de la Clínica:</label>
            <input type="text" name="nombre_clinica" class="form-control" required value="{{ old('nombre_clinica', $cliente->nombre_clinica) }}">
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <input type="text" name="direccion" class="form-control" required value="{{ old('direccion', $cliente->direccion) }}">
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="codigo_postal" class="form-label">Código Postal:</label>
                <input type="text" name="codigo_postal" class="form-control" required value="{{ old('codigo_postal', $cliente->codigo_postal) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="poblacion" class="form-label">Población:</label>
                <input type="text" name="poblacion" class="form-control" required value="{{ old('poblacion', $cliente->poblacion) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="ciudad" class="form-label">Ciudad:</label>
                <input type="text" name="ciudad" class="form-control" required value="{{ old('ciudad', $cliente->ciudad) }}">
            </div>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" name="telefono" class="form-control" required value="{{ old('telefono', $cliente->telefono) }}">
        </div>
        <div class="mb-3">
            <label for="nif" class="form-label">NIF:</label>
            <input type="text" name="nif" class="form-control" required value="{{ old('nif', $cliente->nif) }}">
        </div>
        <button type="submit" class="btn btn-success">Actualizar Cliente</button>
    </form>
@endsection