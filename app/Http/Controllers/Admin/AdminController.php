<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\User;
use App\Models\Restaurante;
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
            // Cargar relaciones necesarias para la vista
            ->with(['restaurante:id,nombre', 'cliente:id,name', 'productos', 'repartidor'])
            ->get();
            
        // 2. Repartidores disponibles (todos) y samples para la inyección de demo
        $repartidores = User::where('rol', 'repartidor')->get(['id', 'name', 'email']);
        $sample_repartidores = User::where('rol', 'repartidor')->limit(3)->get(['id', 'name', 'email']);

        // 3. Restaurantes sample (limitar a 3 para la inyección demo)
        $sample_restaurantes = Restaurante::limit(3)->get(['id', 'nombre']);

        return view('admin.asignacion_ordenes', [
            'ordenes' => $ordenes,
            'repartidores' => $repartidores,
            'sample_repartidores' => $sample_repartidores,
            'sample_restaurantes' => $sample_restaurantes,
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
            return response()->json(['success' => false, 'message' => 'El ID proporcionado no corresponde a un repartidor válido.'], 400);
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
                'message' => 'Repartidor asignado, pero la orden no pudo pasar a "en_camino" automáticamente.', 
                'repartidor_name' => $repartidor->name
            ]);
        }
        
        // La llamada a transicionarA() ya incluye $orden->save() y dispara el Evento Observer.

        return response()->json(['success' => true, 'message' => 'Repartidor asignado y orden en camino.', 'repartidor_name' => $repartidor->name, 'estado' => 'en_camino']);
    }

    /**
     * Mostrar listado de usuarios y permitir cambiar roles (solo para admins).
     */
    public function usuarios()
    {
        // Comprobar que el usuario autenticado sea admin
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            abort(403, 'Acceso no autorizado');
        }

        $users = User::all();

        return view('admin.users', [
            'users' => $users,
        ]);
    }

    /**
     * Actualiza el rol de un usuario (solo admin puede hacerlo).
     */
    public function updateUserRole(Request $request, User $user)
    {
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            abort(403, 'Acceso no autorizado');
        }

        $request->validate([
            'rol' => 'required|in:cliente,restaurante,repartidor,admin',
        ]);

        $user->rol = $request->rol;
        $user->save();

        return redirect()->back()->with('status', 'Rol actualizado.');
    }

    /**
     * API: devuelve JSON con todos los usuarios (solo admin).
     */
    public function apiUsers()
    {
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        $users = User::select('id', 'name', 'email', 'rol')->get();
        return response()->json($users);
    }

    /**
     * API: actualiza rol y responde JSON (solo admin).
     */
    public function apiUpdateUserRole(Request $request, User $user)
    {
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        $data = $request->validate([
            'rol' => 'required|in:cliente,restaurante,repartidor,admin',
        ]);

        $user->rol = $data['rol'];
        $user->save();

        return response()->json(['success' => true, 'rol' => $user->rol]);
    }

    /**
     * API: elimina un usuario (solo admin).
     */
    public function apiDeleteUser(User $user)
    {
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        // Evitar que un admin se borre a sí mismo accidentalmente
        if ($auth->id === $user->id) {
            return response()->json(['error' => 'No puedes eliminar tu propia cuenta.'], 400);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}