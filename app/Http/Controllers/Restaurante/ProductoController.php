<?php

namespace App\Http\Controllers\Restaurante;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Restaurante;
use Illuminate\Http\Request;
// Inertia removed: using Blade views instead

class ProductoController extends Controller
{
    /**
     * Muestra la lista de productos del restaurante autenticado.
     */
    public function index()
    {
        // 1. Obtener el ID del restaurante del usuario autenticado
        $restaurante = auth()->user()->restaurante;

        if (!$restaurante) {
            return redirect()->route('dashboard')->with('error', 'No estás vinculado a un restaurante.');
        }

        // 2. Obtener solo los productos de ese restaurante
        $productos = Producto::where('restaurante_id', $restaurante->id)->get();

        return view('restaurante.productos', [
            'productos' => $productos,
            'restaurante_id' => $restaurante->id,
        ]);
    }
    /**
     * Mostrar formulario para crear un nuevo producto.
     */
    public function create()
    {
        return view('restaurante.producto-create');
    }

    /**
     * Muestra el formulario de edición de un producto.
     */
    public function edit(Producto $producto)
    {
        // Verificar que el producto pertenece al restaurante del usuario autenticado
        if ($producto->restaurante_id !== auth()->user()->restaurante->id) {
            abort(403);
        }

        return view('restaurante.producto-edit', ['producto' => $producto]);
    }

    /**
     * Almacena un nuevo producto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0.01',
            'disponible' => 'sometimes|boolean',
        ]);

        $restaurante = auth()->user()->restaurante;
        if (!$restaurante) {
            return redirect()->route('dashboard')->with('error', 'No estás vinculado a un restaurante.');
        }

        Producto::create([
            'restaurante_id' => $restaurante->id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'disponible' => $request->has('disponible') ? 1 : 0,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(Request $request, Producto $producto)
    {
        // 1. Verificar la propiedad (seguridad: solo puede editar sus propios productos)
        if ($producto->restaurante_id !== auth()->user()->restaurante->id) {
            abort(403);
        }
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0.01',
            'disponible' => 'sometimes|boolean',
        ]);

        $producto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'disponible' => $request->has('disponible') ? 1 : 0,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Cambia la disponibilidad de un producto.
     */
    public function toggleDisponibilidad(Producto $producto)
    {
        // Verificar que el producto pertenece al restaurante del usuario autenticado
        if ($producto->restaurante_id !== auth()->user()->restaurante->id) {
            abort(403);
        }

        $producto->disponible = !$producto->disponible;
        $producto->save();

        return redirect()->back()->with('success', 'Disponibilidad actualizada.');
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Producto $producto)
    {
        if ($producto->restaurante_id !== auth()->user()->restaurante->id) {
            abort(403);
        }

        $producto->delete();
        return redirect()->route('productos.index');
    }

    /**
     * Dashboard para restaurantes
     */
    public function dashboardRestaurante()
    {
        $restaurante = auth()->user()->restaurante;
        
        if (!$restaurante) {
            abort(403, 'No autorizado');
        }

        $totalProductos = Producto::where('restaurante_id', $restaurante->id)->count();
        
        $ordenesPendientes = \App\Models\Orden::where('restaurante_id', $restaurante->id)
            ->where('estado', 'recibida')
            ->count();

        $ordenesEntregadas = \App\Models\Orden::where('restaurante_id', $restaurante->id)
            ->where('estado', 'entregada')
            ->count();

        $productosSinStock = Producto::where('restaurante_id', $restaurante->id)
            ->where('disponible', 0)
            ->count();

        $ordenesPreparar = \App\Models\Orden::where('restaurante_id', $restaurante->id)
            ->where('estado', 'recibida')
            ->with(['cliente', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ordenesEnPrep = \App\Models\Orden::where('restaurante_id', $restaurante->id)
            ->where('estado', 'preparando')
            ->with(['cliente', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $productos = Producto::where('restaurante_id', $restaurante->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurante.dashboard', [
            'restaurante' => $restaurante,
            'totalProductos' => $totalProductos,
            'ordenesPendientes' => $ordenesPendientes,
            'ordenesEntregadas' => $ordenesEntregadas,
            'productosSinStock' => $productosSinStock,
            'ordenesPreparar' => $ordenesPreparar,
            'ordenesEnPrep' => $ordenesEnPrep,
            'productos' => $productos,
        ]);
    }
}