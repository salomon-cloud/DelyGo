<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Orden;
use App\Services\OrdenBuilder; 
use App\EstrategiasEnvio\EnvioEstandar; 
use App\EstrategiasEnvio\EnvioPremium;
use Exception;
// Inertia removed: using Blade views instead

class OrdenController extends Controller
{
    /**
     * Almacena una nueva orden (Implementa Patrón Builder y Strategy).
     */
    public function store(Request $request)
    {
        // Validación básica de los datos de la orden
        $request->validate([
            'direccion_entrega' => 'required|string|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'tipo_envio' => 'nullable|in:estandar,premium',
            'distancia' => 'nullable|numeric|min:0',
        ]);
        
        // 🚨 SIMULACIÓN de datos de entrada necesarios para el Patrón Strategy
        $distanciaSimulada = $request->distancia ?? 5.0; // Distancia en KM (simulada)
        $tipoEnvioElegido = $request->tipo_envio ?? 'estandar'; 
        
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
}


