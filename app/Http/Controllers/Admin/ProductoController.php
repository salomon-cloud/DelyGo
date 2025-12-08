<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Restaurante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    /**
     * Muestra la lista de todos los productos del sistema (admin).
     */
    public function index(Request $request)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            abort(403, 'Acceso no autorizado. Solo administradores pueden gestionar productos.');
        }

        // Obtener todos los productos con su restaurante
        $query = Producto::with('restaurante:id,nombre');

        // Filtro por restaurante si se especifica
        if ($request->filled('restaurante_id')) {
            $query->where('restaurante_id', $request->restaurante_id);
        }

        // Filtro por disponibilidad
        if ($request->filled('disponible')) {
            $query->where('disponible', $request->disponible);
        }

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener todos los restaurantes para el filtro
        $restaurantes = Restaurante::all(['id', 'nombre']);

        return view('admin.productos', [
            'productos' => $productos,
            'restaurantes' => $restaurantes,
            'filtros' => $request->only(['restaurante_id', 'disponible', 'search'])
        ]);
    }

    /**
     * Almacena un nuevo producto.
     */
    public function store(Request $request)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        $request->validate([
            'restaurante_id' => 'required|exists:restaurantes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0.01|max:999999.99',
            'disponible' => 'boolean',
        ]);

        try {
            $producto = Producto::create([
                'restaurante_id' => $request->restaurante_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'disponible' => $request->disponible ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'producto' => $producto->load('restaurante:id,nombre')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra los datos de un producto específico para edición.
     */
    public function edit(Producto $producto)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        return response()->json([
            'success' => true,
            'producto' => $producto->load('restaurante:id,nombre')
        ]);
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(Request $request, Producto $producto)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        $request->validate([
            'restaurante_id' => 'required|exists:restaurantes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0.01|max:999999.99',
            'disponible' => 'boolean',
        ]);

        try {
            $producto->update([
                'restaurante_id' => $request->restaurante_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'disponible' => $request->disponible ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'producto' => $producto->load('restaurante:id,nombre')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Producto $producto)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        try {
            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambia el estado de disponibilidad de un producto.
     */
    public function toggleDisponibilidad(Producto $producto)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        try {
            $producto->disponible = !$producto->disponible;
            $producto->save();

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad actualizada',
                'disponible' => $producto->disponible
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar disponibilidad: ' . $e->getMessage()
            ], 500);
        }
    }
}
