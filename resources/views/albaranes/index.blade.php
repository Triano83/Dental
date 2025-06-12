@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listado de Albaranes</h2>
        <a href="{{ route('albaranes.create') }}" class="btn btn-primary">Crear Nuevo Albarán</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Código Albarán</th>
                <th>Fecha Envío</th>
                <th>Clínica</th>
                <th>Paciente</th>
                <th>Total Albarán</th>
                <th>Facturado</th>
                <th width="220px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($albaranes as $albaran)
                <tr>
                    <td>{{ $albaran->codigo_albaran }}</td>
                    <td>{{ \Carbon\Carbon::parse($albaran->fecha_envio)->format('d/m/Y') }}</td>
                    <td>{{ $albaran->cliente->nombre_clinica ?? 'Cliente Eliminado' }}</td>
                    <td>{{ $albaran->nombre_paciente }}</td>
                    <td>{{ number_format($albaran->total_albaran, 2, ',', '.') }} €</td>
                    <td>
                        @if ($albaran->factura_id)
                            <span class="badge bg-success">Sí</span>
                            {{-- Asegúrate de que esta ruta a facturas también use el nombre correcto del parámetro --}}
                            <a href="{{ route('facturas.show', ['factura' => $albaran->factura_id]) }}" class="btn btn-link btn-sm p-0">Ver Factura</a>
                        @else
                            <span class="badge bg-warning text-dark">No</span>
                        @endif
                    </td>
                    <td>
                        {{-- FORMULARIO DE ELIMINAR: Usa `albarane` como clave del array --}}
                        <form action="{{ route('albaranes.destroy', ['albaran_id' => $albaran->id]) }}" method="POST" style="display:inline;">
                            {{-- ENLACE 'VER': Usa `albarane` como clave del array --}}
                            <a class="btn btn-info btn-sm" href="{{ route('albaranes.show', ['albaran_id' => $albaran->id]) }}">Ver</a>
                            @if (!$albaran->factura_id)
                                {{-- ENLACE 'EDITAR': Usa `albarane` como clave del array --}}
                                <a class="btn btn-primary btn-sm" href="{{ route('albaranes.edit', ['albaran_id' => $albaran->id]) }}">Editar</a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este albarán?')">Eliminar</button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay albaranes registrados aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection