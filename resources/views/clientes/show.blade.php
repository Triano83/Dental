@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalles del Cliente: {{ $cliente->nombre_clinica }}</h2>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card">
        <div class="card-header">
            Información del Cliente
        </div>
        <div class="card-body">
            <p><strong>Nombre de la Clínica:</strong> {{ $cliente->nombre_clinica }}</p>
            <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
            <p><strong>Código Postal:</strong> {{ $cliente->codigo_postal }}</p>
            <p><strong>Población:</strong> {{ $cliente->poblacion }}</p>
            <p><strong>Ciudad:</strong> {{ $cliente->ciudad }}</p>
            <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
            <p><strong>NIF:</strong> {{ $cliente->nif }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-primary">Editar Cliente</a>
        </div>
    </div>
@endsection