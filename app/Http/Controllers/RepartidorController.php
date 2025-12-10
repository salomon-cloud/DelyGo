<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RepartidorController extends Controller
{
    /**
     * Muestra las órdenes asignadas al repartidor autenticado.
     */
    public function misOrdenes()
    {
        $repartidor = Auth::user();

        if (! $repartidor || $repartidor->rol !== 'repartidor') {
            abort(403, 'Acceso denegado. Solo repartidores.');
        }

        // Órdenes en camino o asignadas
        $ordenes = Orden::where('repartidor_id', $repartidor->id)
            ->whereIn('estado', ['en_camino', 'preparando', 'recibida'])
            ->with(['cliente:id,name,email', 'restaurante:id,nombre', 'productos'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('repartidor.mis_ordenes', [
            'ordenes' => $ordenes,
        ]);
    }

    /**
     * Muestra el detalle de una orden para el repartidor.
     */
    public function verOrden(Orden $orden)
    {
        $repartidor = Auth::user();

        if (! $repartidor || $repartidor->rol !== 'repartidor') {
            abort(403, 'Acceso denegado.');
        }

        // Verificar que la orden esté asignada a este repartidor
        if ($orden->repartidor_id !== $repartidor->id) {
            abort(403, 'Esta orden no está asignada a ti.');
        }

        $orden->load(['cliente:id,name,email', 'restaurante:id,nombre', 'productos']);

        return view('repartidor.ver_orden', [
            'orden' => $orden,
        ]);
    }

    /**
     * Actualiza el estado de una orden (repartidor marca como entregada).
     */
    public function actualizarEstado(Request $request, Orden $orden)
    {
        $repartidor = Auth::user();

        if (! $repartidor || $repartidor->rol !== 'repartidor') {
            abort(403, 'Acceso denegado.');
        }

        if ($orden->repartidor_id !== $repartidor->id) {
            abort(403, 'Esta orden no está asignada a ti.');
        }

        $request->validate([
            'nuevo_estado' => 'required|in:en_camino,entregada',
        ]);

        try {
            Log::info('RepartidorController.actualizarEstado - antes', ['orden_id' => $orden->id, 'estado_actual' => $orden->estado, 'nuevo_estado' => $request->nuevo_estado, 'user_id' => $repartidor->id]);

            $orden->transicionarA($request->nuevo_estado);

            // Asegurar que el modelo refleja la BD
            $orden->refresh();

            Log::info('RepartidorController.actualizarEstado - despues', ['orden_id' => $orden->id, 'estado_nuevo' => $orden->estado]);

            // Si la petición espera JSON (AJAX), devolvemos JSON. Si viene desde un formulario normal, redirigimos.
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'estado' => $orden->estado, 'message' => 'Estado actualizado.']);
            }

            return redirect()->back()->with('success', 'Estado actualizado correctamente.');
        } catch (\InvalidArgumentException $e) {
            Log::error('RepartidorController.actualizarEstado - error', ['orden_id' => $orden->id, 'message' => $e->getMessage()]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra el historial de órdenes completadas por el repartidor.
     */
    public function historial()
    {
        $repartidor = Auth::user();

        if (! $repartidor || $repartidor->rol !== 'repartidor') {
            abort(403, 'Acceso denegado.');
        }

        $ordenes = Orden::where('repartidor_id', $repartidor->id)
            ->whereIn('estado', ['entregada', 'cancelada'])
            ->with(['cliente:id,name', 'restaurante:id,nombre', 'rating'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('repartidor.historial', [
            'ordenes' => $ordenes,
        ]);
    }

    /**
     * Dashboard para repartidores
     */
    public function dashboard()
    {
        $repartidor = Auth::user();
        
        if ($repartidor->rol !== 'repartidor') {
            abort(403, 'No autorizado');
        }

        $ordenesAsignadas = Orden::where('repartidor_id', $repartidor->id)
            ->whereIn('estado', ['recibida', 'preparando'])
            ->count();

        $ordenesEnCamino = Orden::where('repartidor_id', $repartidor->id)
            ->where('estado', 'en_camino')
            ->count();

        $ordenesEntregadas = Orden::where('repartidor_id', $repartidor->id)
            ->where('estado', 'entregada')
            ->whereDate('updated_at', today())
            ->count();

        $totalEntregadas = Orden::where('repartidor_id', $repartidor->id)
            ->where('estado', 'entregada')
            ->count();

        $ordenesPendientes = Orden::where('repartidor_id', $repartidor->id)
            ->whereIn('estado', ['recibida', 'preparando'])
            ->with(['cliente', 'restaurante'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ordenesEnCaminoList = Orden::where('repartidor_id', $repartidor->id)
            ->where('estado', 'en_camino')
            ->with(['cliente', 'restaurante'])
            ->get();

        $ordenesEntregadasHoy = Orden::where('repartidor_id', $repartidor->id)
            ->where('estado', 'entregada')
            ->whereDate('updated_at', today())
            ->count();

        $totalEntregasHoy = Orden::where('repartidor_id', $repartidor->id)
            ->where('estado', 'entregada')
            ->whereDate('updated_at', today())
            ->sum('total');

        return view('repartidor.dashboard', [
            'ordenesAsignadas' => $ordenesAsignadas,
            'ordenesEnCamino' => $ordenesEnCamino,
            'ordenesEntregadas' => $ordenesEntregadas,
            'totalEntregadas' => $totalEntregadas,
            'ordenesPendientes' => $ordenesPendientes,
            'ordenesEnCaminoList' => $ordenesEnCaminoList,
            'ordenesEntregadasHoy' => $ordenesEntregadasHoy,
            'totalEntregasHoy' => $totalEntregasHoy,
        ]);
    }
}
