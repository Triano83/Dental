@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listado de Facturas</h2>
        <a href="{{ route('facturas.generar.form') }}" class="btn btn-primary">Generar Nueva Factura</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Número Factura</th>
                <th>Fecha Factura</th>
                <th>Clínica</th>
                <th>Total a Pagar</th>
                <th width="150px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($facturas as $factura)
                <tr>
                    <td>{{ $factura->numero_factura }}</td>
                    <td>{{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y') }}</td>
                    <td>{{ $factura->cliente->nombre_clinica ?? 'Cliente Eliminado' }}</td>
                    <td>{{ number_format($factura->total_a_pagar, 2, ',', '.') }} €</td>
                    <td>
                        {{-- **CORRECCIÓN AQUÍ:** La ruta a facturas.show asume que espera 'factura' como parámetro --}}
                        <a class="btn btn-info btn-sm" href="{{ route('facturas.show', ['factura' => $factura->id]) }}">Ver</a>
                        {{-- Opcional: Botón para descargar PDF de la factura --}}
                        {{-- <a class="btn btn-secondary btn-sm" href="{{ route('facturas.pdf', ['factura' => $factura->id]) }}" target="_blank">PDF</a> --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay facturas generadas aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection