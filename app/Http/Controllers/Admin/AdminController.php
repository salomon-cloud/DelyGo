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
        // 1. Mostrar todas las órdenes para administración (no filtrar) — el UI permite asignar/cambiar estado
        $ordenes = Orden::with(['restaurante:id,nombre', 'cliente:id,name', 'productos', 'repartidor'])
            ->orderBy('id', 'desc')
            ->get();
            
        // 2. Repartidores disponibles (todos) y samples para la inyección de demo
        $repartidores = User::where('rol', 'repartidor')->get(['id', 'name', 'email']);
        $sample_repartidores = User::where('rol', 'repartidor')->limit(3)->get(['id', 'name', 'email']);

    // 3. Restaurantes sample (limitar a 3 para la inyección demo)
    $sample_restaurantes = Restaurante::limit(3)->get(['id', 'nombre']);

    // 4. Productos disponibles (para el modal de creación/selección)
    // Algunos registros pueden tener valores distintos a 1 (por ejemplo 99 en tu BD de pruebas),
    // así que consideramos cualquier valor distinto de 0 como "disponible".
    $productos = \App\Models\Producto::where('disponible', '<>', 0)->get(['id', 'nombre', 'precio', 'restaurante_id']);

        return view('admin.asignacion_ordenes', [
            'ordenes' => $ordenes,
            'repartidores' => $repartidores,
            'sample_repartidores' => $sample_repartidores,
            'sample_restaurantes' => $sample_restaurantes,
            'productos' => $productos,
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

        // 1. Asignar el repartidor y guardar PRIMERO
        $orden->repartidor_id = $repartidor->id;
        $orden->save(); // ✅ Guardar repartidor_id antes de transicionar
        
        // 2.  Cambio de estado automatizado (Patrón State)
        try {
            // Cuando se asigna el repartidor, la orden pasa inmediatamente a 'en_camino'.
            $orden->transicionarA('en_camino');
        } catch (\InvalidArgumentException $e) {
            // Si la orden no estaba en el estado correcto, retornamos error
            return response()->json([
                'success' => false, 
                'message' => 'La orden no puede transicionar a "en_camino" desde su estado actual. Error: ' . $e->getMessage(),
                'repartidor_name' => $repartidor->name
            ], 400);
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

    /**
     * Crea una orden mínima a partir de datos ingresados en el modal y asigna un repartidor.
     * Retorna JSON con success y el id de la nueva orden.
     */
    public function crearYAsignar(Request $request)
    {
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        $data = $request->validate([
            'repartidor_id' => 'required|exists:users,id',
            'restaurante_id' => 'required|exists:restaurantes,id',
            'direccion_entrega' => 'required|string|max:500',
            'total' => 'nullable|numeric',
            'productos' => 'nullable|array',
            // productos expected shape: [{id: integer, cantidad: integer}, ...]
            'productos.*.id' => 'required_with:productos|integer|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|integer|min:1',
            'asignar_ahora' => 'nullable|boolean', // ✅ Agregar validación para asignar_ahora
        ]);

        $asignarAhora = $data['asignar_ahora'] ?? false;

        // Si piden asignar ahora, validar repartidor
        $repartidor = null;
        if ($asignarAhora) {
            $repartidor = User::where('id', $data['repartidor_id'] ?? 0)->where('rol', 'repartidor')->first();
            if (! $repartidor) {
                return response()->json(['success' => false, 'message' => 'Repartidor inválido para asignar ahora'], 400);
            }
        }

        // Crear orden mínima
        $orden = new Orden();
        $orden->restaurante_id = $data['restaurante_id'];
        $orden->direccion_entrega = $data['direccion_entrega'];
        $orden->total = $data['total'] ?? 0;
        $orden->estado = 'preparando';
        // Si la tabla exige cliente_id NOT NULL, asignamos el admin como cliente creador
        $orden->cliente_id = $auth->id;
        $orden->save();

        // Si se pasaron productos, adjuntarlos al pivot orden_producto con la cantidad indicada y precio unitario actual
        if (!empty($data['productos']) && is_array($data['productos'])) {
            $attach = [];
            foreach ($data['productos'] as $item) {
                // cada item debe tener id y cantidad
                $pid = $item['id'] ?? null;
                $cantidad = intval($item['cantidad'] ?? 1);
                if (!$pid) continue;
                $prod = \App\Models\Producto::find($pid);
                $attach[$pid] = [
                    'cantidad' => $cantidad,
                    'precio_unitario' => $prod ? $prod->precio : 0,
                ];
            }
            if (!empty($attach)) {
                $orden->productos()->attach($attach);
            }
        }
        
        // Si piden asignar ahora, asignar y tratar de transicionar
        if ($asignarAhora && $repartidor) {
            $orden->repartidor_id = $repartidor->id;
            $orden->save(); // ✅ Guardar repartidor_id ANTES de transicionar
            try {
                $orden->transicionarA('en_camino');
            } catch (\InvalidArgumentException $e) {
                // guardamos repartidor y devolvemos advertencia
                return response()->json(['success' => true, 'message' => 'Orden creada y repartidor asignado, pero no pudo pasar automáticamente a "en_camino".', 'orden_id' => $orden->id, 'orden' => $orden->toArray(), 'repartidor_name' => $repartidor->name]);
            }

            return response()->json(['success' => true, 'message' => 'Orden creada y repartidor asignado.', 'orden_id' => $orden->id, 'orden' => $orden->toArray(), 'repartidor_name' => $repartidor->name, 'estado' => 'en_camino']);
        }

        // Si no se asignó ahora, devolvemos la orden creada (sin repartidor)
        return response()->json(['success' => true, 'message' => 'Orden creada (sin asignar).', 'orden_id' => $orden->id, 'orden' => $orden->toArray()]);
    }

    /**
     * Permite al admin cambiar el estado de cualquier orden (sin necesidad de ser restaurante).
     */
    public function cambiarEstadoAdmin(Request $request, Orden $orden)
    {
        $auth = auth()->user();
        if (! $auth || $auth->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 403);
        }

        $data = $request->validate(['nuevo_estado' => 'required|string']);

        try {
            $orden->transicionarA($data['nuevo_estado']);
            return response()->json(['success' => true, 'estado' => $orden->estado, 'message' => 'Estado actualizado.']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Dashboard principal del administrador
     */
    public function dashboard()
    {
        // Validar que sea admin
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'No autorizado');
        }

        // Estadísticas globales
        $totalRestaurantes = Restaurante::count();
        $totalProductos = \App\Models\Producto::count();
        $totalOrdenes = Orden::count();
        $totalRepartidores = User::where('rol', 'repartidor')->count();
        $totalUsuarios = User::count();
        $ventasTotal = Orden::sum('total');

        // Detalles de órdenes
        $ordenesRecibidas = Orden::where('estado', 'recibida')->count();
        $ordenesPreparando = Orden::where('estado', 'preparando')->count();
        $ordenesEntregadas = Orden::where('estado', 'entregada')->count();
        $ordenesCanceladas = Orden::where('estado', 'cancelada')->count();
        $ordenesSinAsignar = Orden::whereNull('repartidor_id')->whereNotIn('estado', ['entregada', 'cancelada'])->count();
        $ordenesRestaurantes = Orden::count();

        // Usuarios por rol
        $clientesCount = User::where('rol', 'cliente')->count();
        $restaurantesCount = User::where('rol', 'restaurante')->count();
        $repartidoresCount = User::where('rol', 'repartidor')->count();
        $adminsCount = User::where('rol', 'admin')->count();

        // Repartidores activos (con órdenes entregadas hoy)
        $repartidoresActivos = User::where('rol', 'repartidor')
            ->whereHas('ordenes', function($q) {
                $q->where('updated_at', '>=', now()->subHours(24));
            })
            ->count();

        // Total de entregas
        $totalEntregas = Orden::where('estado', 'entregada')->count();

        // Órdenes recientes (últimas 10)
        $ordenesRecientes = Orden::with(['cliente', 'restaurante', 'repartidor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Últimos usuarios
        $ultimosUsuarios = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Top restaurantes
        $topRestaurantes = Restaurante::withCount('ordenes')
            ->orderBy('ordenes_count', 'desc')
            ->limit(3)
            ->get(['id', 'nombre'])
            ->map(function($r) { 
                return ['nombre' => $r->nombre, 'ordenes_count' => $r->ordenes_count]; 
            })
            ->toArray();

        // Top repartidores
        $topRepartidores = User::where('rol', 'repartidor')
            ->withCount('ordenes')
            ->orderBy('ordenes_count', 'desc')
            ->limit(3)
            ->get(['id', 'name'])
            ->map(function($u) { 
                return ['name' => $u->name, 'entregadas_count' => $u->ordenes_count]; 
            })
            ->toArray();

        return view('admin.admin_dashboard', [
            'totalRestaurantes' => $totalRestaurantes,
            'totalProductos' => $totalProductos,
            'totalOrdenes' => $totalOrdenes,
            'totalRepartidores' => $totalRepartidores,
            'totalUsuarios' => $totalUsuarios,
            'ventasTotal' => $ventasTotal,
            'ordenesRecibidas' => $ordenesRecibidas,
            'ordenesPreparando' => $ordenesPreparando,
            'ordenesEntregadas' => $ordenesEntregadas,
            'ordenesCanceladas' => $ordenesCanceladas,
            'ordenesSinAsignar' => $ordenesSinAsignar,
            'ordenesRestaurantes' => $ordenesRestaurantes,
            'clientesCount' => $clientesCount,
            'restaurantesCount' => $restaurantesCount,
            'repartidoresCount' => $repartidoresCount,
            'adminsCount' => $adminsCount,
            'repartidoresActivos' => $repartidoresActivos,
            'totalEntregas' => $totalEntregas,
            'ordenesRecientes' => $ordenesRecientes,
            'ultimosUsuarios' => $ultimosUsuarios,
            'topRestaurantes' => $topRestaurantes,
            'topRepartidores' => $topRepartidores,
        ]);
    }
}