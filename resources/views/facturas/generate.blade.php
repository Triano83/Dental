@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Generar Facturas</h2>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Volver al Listado de Facturas</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Seleccionar Criterios de Facturación
        </div>
        <div class="card-body">
            <form action="{{ route('facturas.generar') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Clínica (Cliente):</label>
                    <select name="cliente_id" id="cliente_id" class="form-select" required>
                        <option value="">Seleccione una clínica</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre_clinica }} ({{ $cliente->nif }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                        <input type="date" name="fecha_inicio" class="form-control" required value="{{ old('fecha_inicio') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                        <input type="date" name="fecha_fin" class="form-control" required value="{{ old('fecha_fin') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Generar Factura</button>
            </form>
        </div>
    </div>
@endsection