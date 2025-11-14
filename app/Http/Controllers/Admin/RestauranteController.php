<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RestauranteController extends Controller
{
    /**
     * Muestra la lista de restaurantes con opción para crear nuevos.
     */
    public function index()
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            abort(403, 'Acceso no autorizado. Solo administradores pueden gestionar restaurantes.');
        }

        // Obtener todos los restaurantes con su usuario propietario
        $restaurantes = Restaurante::with('user:id,name,email')->orderBy('created_at', 'desc')->get();

        return view('admin.restaurantes', [
            'restaurantes' => $restaurantes,
        ]);
    }

    /**
     * Almacena un nuevo restaurante.
     */
    public function store(Request $request)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'email_usuario' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nombre_restaurante' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'direccion' => 'required|string|max:500',
        ]);

        try {
            // 1. Crear el usuario propietario del restaurante
            $usuario = User::create([
                'name' => $request->nombre_usuario,
                'email' => $request->email_usuario,
                'password' => bcrypt($request->password),
                'rol' => 'restaurante',
            ]);

            // 2. Crear el restaurante vinculado al usuario
            $restaurante = Restaurante::create([
                'user_id' => $usuario->id,
                'nombre' => $request->nombre_restaurante,
                'descripcion' => $request->descripcion,
                'direccion' => $request->direccion,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restaurante creado exitosamente',
                'restaurante' => $restaurante->load('user:id,name,email')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el restaurante: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra los datos de un restaurante específico para edición.
     */
    public function edit(Restaurante $restaurante)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        return response()->json([
            'success' => true,
            'restaurante' => $restaurante->load('user:id,name,email')
        ]);
    }

    /**
     * Actualiza un restaurante existente.
     */
    public function update(Request $request, Restaurante $restaurante)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'email_usuario' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($restaurante->user_id),
            ],
            'nombre_restaurante' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'direccion' => 'required|string|max:500',
        ]);

        try {
            // 1. Actualizar el usuario propietario
            $usuario = $restaurante->user;
            $usuario->update([
                'name' => $request->nombre_usuario,
                'email' => $request->email_usuario,
            ]);

            // Actualizar contraseña solo si se proporciona
            if ($request->filled('password')) {
                $request->validate(['password' => 'string|min:6']);
                $usuario->update(['password' => bcrypt($request->password)]);
            }

            // 2. Actualizar el restaurante
            $restaurante->update([
                'nombre' => $request->nombre_restaurante,
                'descripcion' => $request->descripcion,
                'direccion' => $request->direccion,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restaurante actualizado exitosamente',
                'restaurante' => $restaurante->load('user:id,name,email')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el restaurante: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un restaurante y su usuario asociado.
     */
    public function destroy(Restaurante $restaurante)
    {
        // Verificar que el usuario sea admin
        $auth = Auth::user();
        if (!$auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        try {
            // Obtener el usuario antes de eliminar el restaurante
            $usuario = $restaurante->user;

            // Eliminar el restaurante (esto también eliminará productos por cascade)
            $restaurante->delete();

            // Eliminar el usuario propietario
            if ($usuario) {
                $usuario->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Restaurante eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el restaurante: ' . $e->getMessage()
            ], 500);
        }
    }
}