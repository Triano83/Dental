<?php

namespace App\Http\Controllers;

use App\Models\Albaran;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleAlbaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Asegúrate de que esta línea esté aquí

class AlbaranController extends Controller
{
    // Datos fijos del emisor del albarán (S.M. Dental)
    private $emisor = [
        'nombre' => 'S.M. Dental',
        'direccion' => 'Calle Juan Carlos I nº101, 29130 Alhaurín de la Torre, Málaga',
        'dni' => '25729112R',
        'telefono' => '650311842',
    ];

    /**
     * Muestra una lista de los albaranes.
     */
    public function index()
    {
        $albaranes = Albaran::with('cliente')->latest()->get();
        return view('albaranes.index', compact('albaranes'));
    }

    /**
     * Muestra el formulario para crear un nuevo albarán.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre_clinica')->get();
        $productos = Producto::orderBy('nombre')->get();
        $emisor = $this->emisor;

        return view('albaranes.create', compact('clientes', 'productos', 'emisor'));
    }

    /**
     * Almacena un nuevo albarán en la base de datos.
     */
    public function store(Request $request)
    {
        $rules = [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_envio' => 'required|date',
            'nombre_paciente' => 'required|string|max:255',
            'descuento' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.unidades' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric',
            'productos.*.importe' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $totalProductos = 0;
            $descuentoAplicado = (float)$request->input('descuento', 0);

            foreach ($request->productos as $item) {
                $totalProductos += (float)$item['importe'];
            }

            $totalAlbaran = $totalProductos - $descuentoAplicado;
            if ($totalAlbaran < 0) {
                 $totalAlbaran = 0;
            }

            $albaran = Albaran::create([
                'cliente_id' => $request->cliente_id,
                'fecha_envio' => $request->fecha_envio,
                'nombre_paciente' => $request->nombre_paciente,
                'descuento' => $descuentoAplicado,
                'total_productos' => $totalProductos,
                'total_albaran' => $totalAlbaran,
                'codigo_albaran' => 'PENDIENTE_GENERAR',
                'factura_id' => null,
            ]);

            $fechaFormato = Carbon::parse($albaran->fecha_envio)->format('Ymd');
            $albaran->codigo_albaran = $fechaFormato . str_pad($albaran->id, 2, '0', STR_PAD_LEFT);
            $albaran->save();

            foreach ($request->productos as $item) {
                DetalleAlbaran::create([
                    'albaran_id' => $albaran->id,
                    'producto_id' => $item['producto_id'],
                    'nombre_producto' => Producto::find($item['producto_id'])->nombre,
                    'unidades' => (int)$item['unidades'],
                    'precio_unitario' => (float)$item['precio_unitario'],
                    'importe' => (float)$item['importe'],
                ]);
            }

            DB::commit();

            return redirect()->route('albaranes.index')
                             ->with('success', 'Albarán creado exitosamente. Código: ' . $albaran->codigo_albaran);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al crear el albarán: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un albarán específico.
     */
    public function show(Albaran $albaran)
    {
        $albaran->load('cliente', 'detalleAlbaranes.producto');
        $emisor = $this->emisor;

        return view('albaranes.show', compact('albaran', 'emisor'));
    }

    /**
     * Muestra el formulario para editar un albarán existente.
     */
    public function edit(Albaran $albaran)
    {
        $clientes = Cliente::orderBy('nombre_clinica')->get();
        $productos = Producto::orderBy('nombre')->get();
        $albaran->load('detalleAlbaranes');

        return view('albaranes.edit', compact('albaran', 'clientes', 'productos'));
    }

    /**
     * Actualiza un albarán existente en la base de datos.
     */
    public function update(Request $request, Albaran $albaran)
    {
        $rules = [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_envio' => 'required|date',
            'nombre_paciente' => 'required|string|max:255',
            'descuento' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.unidades' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric',
            'productos.*.importe' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $albaran->detalleAlbaranes()->delete();

            $totalProductos = 0;
            $descuentoAplicado = (float)$request->input('descuento', 0);

            foreach ($request->productos as $item) {
                DetalleAlbaran::create([
                    'albaran_id' => $albaran->id,
                    'producto_id' => $item['producto_id'],
                    'nombre_producto' => Producto::find($item['producto_id'])->nombre,
                    'unidades' => (int)$item['unidades'],
                    'precio_unitario' => (float)$item['precio_unitario'],
                    'importe' => (float)$item['importe'],
                ]);
                $totalProductos += (float)$item['importe'];
            }

            $totalAlbaran = $totalProductos - $descuentoAplicado;
            if ($totalAlbaran < 0) {
                 $totalAlbaran = 0;
            }

            $albaran->update([
                'cliente_id' => $request->cliente_id,
                'fecha_envio' => $request->fecha_envio,
                'nombre_paciente' => $request->nombre_paciente,
                'descuento' => $descuentoAplicado,
                'total_productos' => $totalProductos,
                'total_albaran' => $totalAlbaran,
            ]);

            DB::commit();

            return redirect()->route('albaranes.index')
                             ->with('success', 'Albarán actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el albarán: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un albarán de la base de datos.
     */
    public function destroy(Albaran $albaran)
    {
        // Verificar si el albarán ya ha sido facturado
        if ($albaran->factura_id) {
            return redirect()->back()->with('error', 'No se puede eliminar un albarán que ya ha sido facturado.');
        }

        // Eliminar el albarán (incluirá los detalles gracias a onDelete('cascade') en la migración)
        $albaran->delete();

        return redirect()->route('albaranes.index')
                         ->with('success', 'Albarán eliminado exitosamente.');
    }
}