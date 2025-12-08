<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Orden;
use App\Http\Requests\StoreRatingRequest;

class RatingController extends Controller
{
    /**
     * Store a newly created rating.
     */
    public function store(StoreRatingRequest $request)
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->back()->with('error', 'Debe iniciar sesión para calificar.');
        }

        $data = $request->validated();

        $orden = Orden::find($data['orden_id']);
        if (! $orden) {
            return redirect()->back()->with('error', 'Orden no encontrada.');
        }

        // Solo el cliente asociado puede calificar su orden
        if ($orden->cliente_id !== $user->id) {
            abort(403, 'No autorizado para calificar esta orden.');
        }

        // Solo permitir calificar si la orden está entregada
        if ($orden->estado !== 'entregada') {
            return redirect()->back()->with('error', 'Solo puedes calificar órdenes entregadas.');
        }

        // Evitar doble calificación
        if ($orden->rating) {
            return redirect()->back()->with('status', 'Esta orden ya fue calificada.');
        }

        $rating = Rating::create([
            'orden_id' => $orden->id,
            'cliente_id' => $user->id,
            'repartidor_id' => $orden->repartidor_id,
            'puntuacion' => $data['puntuacion'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()->back()->with('status', 'Gracias por tu calificación.');
    }
}
