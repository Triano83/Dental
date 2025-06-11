<?php

namespace App\Http\Controllers;

use App\Models\Albaran;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleAlbaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // ¡IMPORTANTE: Añadir esta línea!


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
        // Carga ansiosa para el cliente para evitar el problema N+1
        $albaranes = Albaran::with('cliente')->latest()->get(); // Ordenar por los más recientes
        return view('albaranes.index', compact('albaranes'));
    }

    /**
     * Muestra el formulario para crear un nuevo albarán.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre_clinica')->get();
        $productos = Producto::orderBy('nombre')->get();
        $emisor = $this->emisor; // Pasamos los datos del emisor a la vista

        return view('albaranes.create', compact('clientes', 'productos', 'emisor'));
    }

    /**
     * Almacena un nuevo albarán en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_envio' => 'required|date',
            'nombre_paciente' => 'required|string|max:255',
            'descuento' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1', // Debe haber al menos un producto
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.unidades' => 'required|integer|min:1',
            // El precio unitario se toma de la BD, no se valida aquí directamente
        ]);

        // Iniciar una transacción de base de datos
        DB::beginTransaction();

        try {
            $totalProductos = 0;
            $descuentoAplicado = $request->input('descuento', 0); // Si no viene descuento, es 0

            // Primero, calcula el total de productos para poder generar el código de albarán si es necesario
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);
                $totalProductos += $producto->precio * $item['unidades'];
            }

            // Calcula el total final del albarán
            $totalAlbaran = $totalProductos - $descuentoAplicado;
            if ($totalAlbaran < 0) {
                 $totalAlbaran = 0; // Evitar totales negativos por descuentos excesivos
            }


            // Generar el código de albarán (AAAAMMDD + ID)
            // Para el ID, necesitamos el ID del albarán recién creado,
            // lo cual es un poco complicado antes de la creación.
            // Una estrategia común es generarlo después de la creación
            // o usar un número consecutivo global o por día.
            // Por simplicidad, por ahora usaremos un timestamp y el ID del albarán
            // Podemos mejorar esto más tarde para un consecutivo por día/mes si es estricto.
            // Para la primera versión, generaremos un ID simple o usaremos la fecha + un número consecutivo.
            // La forma más robusta es obtener el último ID de albarán del día o del mes.
            // Por ahora, usaremos Carbon para el prefijo de fecha y un valor que se actualizará.

            $albaran = Albaran::create([
                'cliente_id' => $request->cliente_id,
                'fecha_envio' => $request->fecha_envio,
                'nombre_paciente' => $request->nombre_paciente,
                'descuento' => $descuentoAplicado,
                'total_productos' => $totalProductos,
                'total_albaran' => $totalAlbaran,
                'codigo_albaran' => 'PENDIENTE_GENERAR', // Marcador temporal
                'factura_id' => null, // Inicialmente null
            ]);

            // Ahora que tenemos el ID del albarán, generamos el código real.
            $fechaFormato = \Carbon\Carbon::parse($albaran->fecha_envio)->format('Ymd');
            $albaran->codigo_albaran = $fechaFormato . str_pad($albaran->id, 2, '0', STR_PAD_LEFT); // AAAAMMDD + ID con 2 digitos

            // Guardamos el código de albarán final
            $albaran->save();


            // Guardar los detalles del albarán
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);
                DetalleAlbaran::create([
                    'albaran_id' => $albaran->id,
                    'producto_id' => $producto->id,
                    'nombre_producto' => $producto->nombre, // Copia del nombre
                    'unidades' => $item['unidades'],
                    'precio_unitario' => $producto->precio, // Copia del precio
                    'importe' => $producto->precio * $item['unidades'],
                ]);
            }

            DB::commit(); // Confirmar la transacción

            return redirect()->route('albaranes.index')
                             ->with('success', 'Albarán creado exitosamente. Código: ' . $albaran->codigo_albaran);

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return redirect()->back()->withInput()->with('error', 'Error al crear el albarán: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un albarán específico.
     */
    public function show(Albaran $albaran)
    {
        // Carga ansiosa para el cliente y los detalles del albarán
        $albaran->load('cliente', 'detalleAlbaranes.producto');
        $emisor = $this->emisor; // Pasamos los datos del emisor a la vista

        return view('albaranes.show', compact('albaran', 'emisor'));
    }

    /**
     * Muestra el formulario para editar un albarán existente.
     * Nota: La edición de albaranes puede ser compleja si ya están facturados o tienen un flujo de trabajo.
     * Por simplicidad inicial, permitiremos la edición. Podrías restringirla más adelante.
     */
    public function edit(Albaran $albaran)
    {
        $clientes = Cliente::orderBy('nombre_clinica')->get();
        $productos = Producto::orderBy('nombre')->get();
        $albaran->load('detalleAlbaranes'); // Cargar los detalles existentes

        return view('albaranes.edit', compact('albaran', 'clientes', 'productos'));
    }

    /**
     * Actualiza un albarán existente en la base de datos.
     */
    public function update(Request $request, Albaran $albaran)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_envio' => 'required|date',
            'nombre_paciente' => 'required|string|max:255',
            'descuento' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.unidades' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Eliminar detalles de albaranes antiguos para el albarán actual
            $albaran->detalleAlbaranes()->delete();

            $totalProductos = 0;
            $descuentoAplicado = $request->input('descuento', 0);

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);
                DetalleAlbaran::create([
                    'albaran_id' => $albaran->id,
                    'producto_id' => $producto->id,
                    'nombre_producto' => $producto->nombre,
                    'unidades' => $item['unidades'],
                    'precio_unitario' => $producto->precio,
                    'importe' => $producto->precio * $item['unidades'],
                ]);
                $totalProductos += $producto->precio * $item['unidades'];
            }

            $totalAlbaran = $totalProductos - $descuentoAplicado;
            if ($totalAlbaran < 0) {
                 $totalAlbaran = 0;
            }

            // Actualizar el albarán principal
            $albaran->update([
                'cliente_id' => $request->cliente_id,
                'fecha_envio' => $request->fecha_envio,
                'nombre_paciente' => $request->nombre_paciente,
                'descuento' => $descuentoAplicado,
                'total_productos' => $totalProductos,
                'total_albaran' => $totalAlbaran,
                // El código de albarán no se actualiza ya que es único y basado en la fecha de creación.
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
        // Opcional: Impedir la eliminación si el albarán ya está facturado.
        if ($albaran->factura_id) {
            return redirect()->back()->with('error', 'No se puede eliminar un albarán que ya ha sido facturado.');
        }

        $albaran->delete(); // Esto también eliminará los detalles del albarán por `onDelete('cascade')`

        return redirect()->route('albaranes.index')
                         ->with('success', 'Albarán eliminado exitosamente.');
    }
} 