@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalles del Albarán: {{ $albaran->codigo_albaran }}</h2>
        <a href="{{ route('albaranes.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Información del Albarán
            <span class="float-end">
                @if ($albaran->factura_id)
                    <span class="badge bg-success">Facturado</span>
                    <a href="{{ route('facturas.show', $albaran->factura_id) }}" class="btn btn-link btn-sm p-0 text-white">Ver Factura</a>
                @else
                    <span class="badge bg-warning text-dark">Pendiente de Facturar</span>
                @endif
            </span>
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
                    @if ($albaran->cliente)
                        <p><strong>Nombre:</strong> {{ $albaran->cliente->nombre_clinica }}</p>
                        <p><strong>Dirección:</strong> {{ $albaran->cliente->direccion }}, {{ $albaran->cliente->codigo_postal }} {{ $albaran->cliente->poblacion }}, {{ $albaran->cliente->ciudad }}</p>
                        <p><strong>NIF:</strong> {{ $albaran->cliente->nif }}</p>
                        <p><strong>Teléfono:</strong> {{ $albaran->cliente->telefono }}</p>
                    @else
                        <p class="text-danger">Cliente no encontrado o eliminado.</p>
                    @endif
                </div>
            </div>
            <hr>
            <p><strong>Código de Albarán:</strong> {{ $albaran->codigo_albaran }}</p>
            <p><strong>Fecha de Envío:</strong> {{ \Carbon\Carbon::parse($albaran->fecha_envio)->format('d/m/Y') }}</p>
            <p><strong>Nombre del Paciente:</strong> {{ $albaran->nombre_paciente }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Detalle de Productos</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Unidades</th>
                        <th>Precio Unitario (€)</th>
                        <th>Importe (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($albaran->detalleAlbaranes as $detalle)
                        <tr>
                            <td>{{ $detalle->nombre_producto }}</td>
                            <td>{{ $detalle->unidades }}</td>
                            <td>{{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                            <td>{{ number_format($detalle->importe, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end">
                <p><strong>Total Suma de Productos:</strong> {{ number_format($albaran->total_productos, 2, ',', '.') }} €</p>
                <p><strong>Descuento:</strong> {{ number_format($albaran->descuento, 2, ',', '.') }} €</p>
                <h4><strong>Total del Albarán: {{ number_format($albaran->total_albaran, 2, ',', '.') }} €</strong></h4>
            </div>
        </div>
        <div class="card-footer text-end">
            @if (!$albaran->factura_id)
                {{-- Aquí se corrige la ruta para 'Editar' --}}
                <a href="{{ route('albaranes.edit', ['albarane' => $albaran->id]) }}" class="btn btn-primary">Editar Albarán</a>
            @endif
            {{-- Aquí podríamos añadir un botón para generar PDF --}}
            <a href="{{ route('albaranes.pdf', ['albarane' => $albaran->id]) }}" class="btn btn-info" target="_blank">Descargar PDF</a>
        </div>
    </div>
@endsection