@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listado de Clientes</h2>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">Crear Nuevo Cliente</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Clínica</th>
                <th>Población</th>
                <th>Teléfono</th>
                <th>NIF</th>
                <th width="200px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id }}</td>
                    <td>{{ $cliente->nombre_clinica }}</td>
                    <td>{{ $cliente->poblacion }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>{{ $cliente->nif }}</td>
                    <td>
                        <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST">
                            <a class="btn btn-info btn-sm" href="{{ route('clientes.show', $cliente->id) }}">Ver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('clientes.edit', $cliente->id) }}">Editar</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay clientes registrados aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection