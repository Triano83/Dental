<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de los clientes.
     */
    public function index()
    {
        $clientes = Cliente::all(); // Obtiene todos los clientes
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Almacena un nuevo cliente en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'nombre_clinica' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'poblacion' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'nif' => 'required|string|max:20|unique:clientes', // NIF debe ser único
        ]);

        Cliente::create($request->all()); // Crea el cliente

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Muestra los detalles de un cliente específico.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Muestra el formulario para editar un cliente existente.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualiza un cliente existente en la base de datos.
     */
    public function update(Request $request, Cliente $cliente)
    {
        // Validación de los datos (excluir el NIF del cliente actual para unique)
        $request->validate([
            'nombre_clinica' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'poblacion' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'nif' => 'required|string|max:20|unique:clientes,nif,' . $cliente->id,
        ]);

        $cliente->update($request->all()); // Actualiza el cliente

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Elimina un cliente de la base de datos.
     */
    public function destroy(Cliente $cliente)
    {
        // Considerar si un cliente puede ser eliminado si tiene albaranes/facturas asociados.
        // Por ahora, lo permitimos, pero en un sistema real, podrías necesitar una "eliminación suave" (soft delete)
        // o impedir la eliminación si hay registros relacionados.
        $cliente->delete();

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente eliminado exitosamente.');
    }
}