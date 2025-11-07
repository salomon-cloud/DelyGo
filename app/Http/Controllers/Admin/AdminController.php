<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\User;
// Inertia removed: using Blade views instead

class AdminController extends Controller
{
    /**
     * Muestra la vista de administración para la asignación de órdenes.
     * Carga órdenes pendientes y repartidores disponibles.
     */
    public function showAsignacion()
    {
        // 1. Órdenes que necesitan ser asignadas (estado 'preparando' y sin repartidor)
        $ordenes = Orden::where('estado', 'preparando')
            ->whereNull('repartidor_id')
            // 🎯 Cargar el nombre del Restaurante y del Cliente (esencial para la vista)
            ->with('restaurante:id,nombre')
            ->with('cliente:id,name') 
            ->get();
            
        // 2. Repartidores disponibles
        $repartidores = User::where('rol', 'repartidor')->get(['id', 'name']);

        return view('admin.asignacion_ordenes', [
            'ordenes' => $ordenes,
            'repartidores' => $repartidores,
        ]);
    }
    
    /**
     * Asigna un repartidor a una orden específica y cambia el estado.
     * @param \App\Models\Orden $orden La orden a modificar.
     */
    public function asignarRepartidor(Request $request, Orden $orden)
    {
        // Validación de que el repartidor exista y tenga el rol correcto
        $request->validate([
            'repartidor_id' => 'required|exists:users,id',
        ]);

        $repartidor = User::where('id', $request->repartidor_id)
                          ->where('rol', 'repartidor')
                          ->first();

        if (!$repartidor) {
            return response()->json(['error' => 'El ID proporcionado no corresponde a un repartidor válido.'], 400);
        }

        // 1. Asignar el repartidor
        $orden->repartidor_id = $repartidor->id;
        // La orden debe estar en 'preparando' para poder ser asignada.
        
        // 2. 🎯 Cambio de estado automatizado (Patrón State)
        try {
            // Cuando se asigna el repartidor, la orden pasa inmediatamente a 'en_camino'.
            $orden->transicionarA('en_camino');
        } catch (\InvalidArgumentException $e) {
            // Si la orden no estaba en el estado correcto, solo guardamos el repartidor y lanzamos una advertencia
            $orden->save(); 
            return response()->json([
                'success' => true, 
                'mensaje' => 'Repartidor asignado, pero la orden no pudo pasar a "en_camino" automáticamente.', 
                'repartidor' => $repartidor->name
            ]);
        }
        
        // La llamada a transicionarA() ya incluye $orden->save() y dispara el Evento Observer.

        return response()->json(['success' => true, 'mensaje' => 'Repartidor asignado y orden en camino.', 'repartidor' => $repartidor->name]);
    }
}