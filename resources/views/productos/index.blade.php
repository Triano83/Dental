@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listado de Productos</h2>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">Crear Nuevo Producto</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th width="200px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ number_format($producto->precio, 2, ',', '.') }} €</td> {{-- Formatear precio para España --}}
                    <td>
                        <form action="{{ route('productos.destroy', $producto->id) }}" method="POST">
                            <a class="btn btn-info btn-sm" href="{{ route('productos.show', $producto->id) }}">Ver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('productos.edit', $producto->id) }}">Editar</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay productos registrados aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection