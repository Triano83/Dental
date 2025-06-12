@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalles de la Factura: {{ $factura->numero_factura }}</h2>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Información de la Factura
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Datos del Emisor:</h6>
                    <p><strong>Nombre:</strong> {{ $emisor['nombre'] }}</p>
                    <p><strong>Dirección:</strong> {{ $emisor['direccion'] }}</p>
                    <p><strong>DNI:</strong> {{ $emisor['dni'] }}</p>
                    <p><strong>Teléfono:</strong> {{ $emisor['telefono'] }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Datos de la Clínica (Receptor):</h6>
                    @if ($factura->cliente)
                        <p><strong>Nombre:</strong> {{ $factura->cliente->nombre_clinica }}</p>
                        <p><strong>Dirección:</strong> {{ $factura->cliente->direccion }}, {{ $factura->cliente->codigo_postal }} {{ $factura->cliente->poblacion }}, {{ $factura->cliente->ciudad }}</p>
                        <p><strong>NIF:</strong> {{ $factura->cliente->nif }}</p>
                        <p><strong>Teléfono:</strong> {{ $factura->cliente->telefono }}</p>
                    @else
                        <p class="text-danger">Cliente no encontrado o eliminado.</p>
                    @endif
                </div>
            </div>
            <hr>
            <p><strong>Número de Factura:</strong> {{ $factura->numero_factura }}</p>
            <p><strong>Fecha de Factura:</strong> {{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Albaranes Incluidos en la Factura</div>
        <div class="card-body">
            @if ($factura->albaranes->isNotEmpty())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Código Albarán</th>
                            <th>Fecha Envío</th>
                            <th>Nombre Paciente</th>
                            <th>Importe (€)</th>
                            <th width="100px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($factura->albaranes as $albaran)
                            <tr>
                                <td>{{ $albaran->codigo_albaran }}</td>
                                <td>{{ \Carbon\Carbon::parse($albaran->fecha_envio)->format('d/m/Y') }}</td>
                                <td>{{ $albaran->nombre_paciente }}</td>
                                <td>{{ number_format($albaran->total_albaran, 2, ',', '.') }}</td>
                                <td><a href="{{ route('albaranes.show', ['albarane' => $albaran->id]) }}" class="btn btn-info btn-sm">Ver Albarán</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No se encontraron albaranes asociados a esta factura.</p>
            @endif
            <div class="text-end mt-4">
                <h4><strong>Total a Pagar de la Factura: {{ number_format($factura->total_a_pagar, 2, ',', '.') }} €</strong></h4>
            </div>
        </div>
        <div class="card-footer text-end">
            {{-- Opcional: Botón para descargar PDF de la factura --}}
            {{-- <a class="btn btn-info" href="{{ route('facturas.pdf', $factura->id) }}" target="_blank">Descargar PDF</a> --}}
        </div>
    </div>
@endsection