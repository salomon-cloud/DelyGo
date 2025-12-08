<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Orden;
use App\Services\OrdenBuilder; 
use App\EstrategiasEnvio\EnvioEstandar; 
use App\EstrategiasEnvio\EnvioPremium;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreOrdenRequest;
use Exception;
// Inertia removed: using Blade views instead

class OrdenController extends Controller
{
    /**
     * Muestra el historial de órdenes del cliente autenticado.
     */
    public function index()
    {
        $cliente = auth()->user();
        if (! $cliente) {
            abort(401, 'Debes estar autenticado.');
        }

        // Obtener todas las órdenes del cliente, ordenadas por más recientes
        $ordenes = Orden::where('cliente_id', $cliente->id)
            ->with(['restaurante:id,nombre', 'repartidor:id,name', 'rating'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.ordenes_historial', [
            'ordenes' => $ordenes,
        ]);
    }

    /**
     * Muestra el detalle y tracking de una orden específica.
     * @param \App\Models\Orden $orden La orden a trackear.
     */
    public function show(Orden $orden)
    {
        // Verificar propiedad (seguridad)
        if ($orden->cliente_id !== auth()->id()) {
            abort(403, 'Acceso denegado. No eres el dueño de esta orden.');
        }

        // Cargar relaciones
        $orden->load(['restaurante:id,nombre', 'repartidor:id,name', 'productos', 'cliente:id,name,email', 'rating']);

        return view('cliente.tracking', [
            'orden' => $orden,
        ]);
    }
    /**
     * Almacena una nueva orden (Implementa Patrón Builder y Strategy).
     */
    public function store(StoreOrdenRequest $request)
    {
        // Validación realizada por StoreOrdenRequest
        $validated = $request->validated();
        
        // 🚨 SIMULACIÓN de datos de entrada necesarios para el Patrón Strategy
        $distanciaSimulada = 5.0; // Distancia en KM (simulada)
        $tipoEnvioElegido = 'estandar'; 
        
        // 1. 🎯 Elegir la Estrategia de Envío (Patrón Strategy)
        $estrategia = match ($tipoEnvioElegido) {
            'premium' => new EnvioPremium(),
            default => new EnvioEstandar(),
        };

        try {
            // 2. Inicializar el Builder con la nueva firma del constructor
            $builder = new OrdenBuilder(
                auth()->id(), 
                $request->direccion_entrega, 
                $distanciaSimulada,      // Distancia
                $estrategia              // Estrategia de envío
            );

            // 3. Añadir productos al Builder
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);
                $builder->agregarProducto($producto, $item['cantidad']);
            }
            
            // 4. Obtener la Orden (incluye el cálculo del total con envío)
            $ordenFinal = $builder->obtenerOrden();

            return response()->json([
                'mensaje' => 'Orden creada con éxito. Total: ' . $ordenFinal->total, 
                'orden_id' => $ordenFinal->id
            ], 201);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Muestra la vista de tracking para una orden específica (Cliente).
     * @param \App\Models\Orden $orden La orden a trackear.
     */
    public function showTracking(Orden $orden)
    {
        // 1. Verificar la propiedad (seguridad)
        if ($orden->cliente_id !== auth()->id()) {
            abort(403, 'Acceso denegado. No eres el dueño de esta orden.');
        }

        // 2. Cargar el cliente (necesario para el canal privado de Echo en Vue.js)
        $orden->load('cliente'); 

        // 3. Enviar la orden y su URL de mapa estático (Accessor) a la vista de Vue
        return view('cliente.tracking', [
            'orden' => $orden->only([
                'id', 
                'estado', 
                'cliente_id', 
                'direccion_entrega', 
                'mapa_estatico_url'
            ]),
        ]);
    }

    /**
     * Muestra las órdenes pendientes para el restaurante autenticado.
     * @return \Inertia\Response
     */
    public function pendientes()
    {
        // Asumimos que el usuario autenticado tiene la relación 'restaurante'
        if (!auth()->user()->restaurante) {
            abort(403, 'El usuario no está vinculado a un restaurante.');
        }

        $restauranteId = auth()->user()->restaurante->id;

        // Obtener órdenes en estados activos
        $ordenes = Orden::where('restaurante_id', $restauranteId)
            ->whereIn('estado', ['recibida', 'preparando', 'en_camino'])
            ->with('cliente:id,name') 
            ->get();

        return view('restaurante.ordenes_pendientes', [
            'ordenes' => $ordenes,
        ]);
    }
    
    /**
     * Permite al restaurante cambiar el estado de la orden (Interactúa con Patrón State).
     * @param \App\Models\Orden $orden La orden a modificar.
     */
    public function cambiarEstado(Request $request, Orden $orden)
    {
        $request->validate(['nuevo_estado' => 'required|string']);

        // 1. Seguridad: Verificar que la orden pertenezca al restaurante
        if ($orden->restaurante_id !== auth()->user()->restaurante->id) {
             abort(403, 'Esta orden no pertenece a tu restaurante.');
        }

        try {
            // 2. 🎯 Llama al método que usa el Patrón State
            $orden->transicionarA($request->nuevo_estado);

            return response()->json(['success' => true, 'estado' => $orden->estado, 'message' => 'Estado cambiado.']);
            
        } catch (\InvalidArgumentException $e) {
            // 3. Si el Patrón State prohíbe la transición, devuelve error 400
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Dashboard para clientes
     */
    public function dashboardCliente()
    {
        $cliente = auth()->user();
        if ($cliente->rol !== 'cliente') {
            abort(403, 'No autorizado');
        }

        $totalOrdenes = Orden::where('cliente_id', $cliente->id)->count();
        $ordenesActivas = Orden::where('cliente_id', $cliente->id)
            ->whereIn('estado', ['recibida', 'preparando', 'en_camino'])
            ->count();
        $ordenesEntregadas = Orden::where('cliente_id', $cliente->id)
            ->where('estado', 'entregada')
            ->count();
        $ordenesCanceladas = Orden::where('cliente_id', $cliente->id)
            ->where('estado', 'cancelada')
            ->count();

        $ordenesActuales = Orden::where('cliente_id', $cliente->id)
            ->whereIn('estado', ['recibida', 'preparando', 'en_camino'])
            ->with(['restaurante', 'repartidor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ultimasOrdenes = Orden::where('cliente_id', $cliente->id)
            ->with(['restaurante'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('cliente.dashboard', [
            'totalOrdenes' => $totalOrdenes,
            'ordenesActivas' => $ordenesActivas,
            'ordenesEntregadas' => $ordenesEntregadas,
            'ordenesCanceladas' => $ordenesCanceladas,
            'ordenesActuales' => $ordenesActuales,
            'ultimasOrdenes' => $ultimasOrdenes,
        ]);
    }
}


