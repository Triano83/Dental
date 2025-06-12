<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Albaran; // Necesitamos el modelo Albaran para la lógica de facturación
use App\Models\Cliente; // Necesitamos el modelo Cliente
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Para transacciones
use Carbon\Carbon; // Para manejar fechas

class FacturaController extends Controller
{
    // Datos fijos del emisor de la factura (S.M. Dental)
    private $emisor = [
        'nombre' => 'S.M. Dental',
        'direccion' => 'Calle Juan Carlos I nº101, 29130 Alhaurín de la Torre, Málaga',
        'dni' => '25729112R',
        'telefono' => '650311842',
    ];

    /**
     * Muestra una lista de las facturas.
     */
    public function index()
    {
        // Carga ansiosa para el cliente para evitar el problema N+1
        $facturas = Factura::with('cliente')->latest()->get();
        $emisor = $this->emisor; // Pasamos los datos del emisor a la vista

        return view('facturas.index', compact('facturas', 'emisor'));
    }

    /**
     * Muestra los detalles de una factura específica.
     */
    public function show(Factura $factura)
    {
        // Carga ansiosa para el cliente y los albaranes relacionados
        $factura->load('cliente', 'albaranes');
        $emisor = $this->emisor; // Pasamos los datos del emisor a la vista

        return view('facturas.show', compact('factura', 'emisor'));
    }

    /**
     * Muestra el formulario para generar facturas.
     */
    public function showGenerateForm()
    {
        $clientes = Cliente::orderBy('nombre_clinica')->get();
        return view('facturas.generate', compact('clientes'));
    }

    /**
     * Genera facturas para un cliente y período de tiempo.
     */
    public function generarFacturas(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $clienteId = $request->cliente_id;
        $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();

        // Buscar albaranes no facturados para el cliente y período
        $albaranesPendientes = Albaran::where('cliente_id', $clienteId)
            ->whereBetween('fecha_envio', [$fechaInicio, $fechaFin])
            ->whereNull('factura_id') // Solo albaranes que no estén facturados
            ->get();

        if ($albaranesPendientes->isEmpty()) {
            return redirect()->back()->with('info', 'No se encontraron albaranes pendientes para facturar en el período y cliente seleccionados.');
        }

        DB::beginTransaction();

        try {
            $totalFactura = 0;
            foreach ($albaranesPendientes as $albaran) {
                $totalFactura += $albaran->total_albaran;
            }

            // Obtener el último número de factura del día (o del mes/año, según la lógica)
            // Para la primera versión, generaremos un número consecutivo simple por fecha de emisión.
            // Para un número ID más robusto, necesitaríamos una tabla de configuración o un campo en Facturas.
            $fechaActual = Carbon::now();
            $prefixFactura = $fechaActual->format('Ymd');
            
            // Buscar la última factura con el mismo prefijo de fecha para calcular el consecutivo
            $ultimaFacturaHoy = Factura::where('numero_factura', 'like', $prefixFactura . '%')
                                       ->orderBy('numero_factura', 'desc')
                                       ->first();
            
            $consecutivo = 1;
            if ($ultimaFacturaHoy) {
                // Extraer el número consecutivo de la última factura (ej. 20250612XX -> XX)
                $lastConsecutive = intval(substr($ultimaFacturaHoy->numero_factura, 8));
                $consecutivo = $lastConsecutive + 1;
            }
            
            $numeroFactura = $prefixFactura . str_pad($consecutivo, 2, '0', STR_PAD_LEFT);


            $factura = Factura::create([
                'cliente_id' => $clienteId,
                'numero_factura' => $numeroFactura,
                'fecha_factura' => $fechaActual->toDateString(),
                'total_a_pagar' => $totalFactura,
            ]);

            // Enlazar los albaranes a la factura recién creada
            foreach ($albaranesPendientes as $albaran) {
                $albaran->factura_id = $factura->id;
                $albaran->save();
            }

            DB::commit();

            return redirect()->route('facturas.index')
                             ->with('success', 'Factura ' . $factura->numero_factura . ' generada exitosamente para el cliente ' . $factura->cliente->nombre_clinica . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al generar la factura: ' . $e->getMessage());
        }
    }

    // No necesitamos métodos create, store, edit, update, destroy para facturas si se generan desde albaranes.
    // Si en el futuro quieres poder editar/eliminar facturas directamente, se añadirían aquí.
}