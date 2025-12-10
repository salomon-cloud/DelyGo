<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Cliente\OrdenController;
Route::get('/', function () {
    return view('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->rol === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->rol === 'cliente') {
        return redirect()->route('cliente.dashboard');
    } elseif ($user->rol === 'repartidor') {
        return redirect()->route('repartidor.dashboard');
    } elseif ($user->rol === 'restaurante') {
        return redirect()->route('restaurante.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== DASHBOARDS POR ROL =====
    // Dashboard Admin
    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Dashboard Cliente
    Route::get('/cliente/dashboard', [\App\Http\Controllers\Cliente\OrdenController::class, 'dashboardCliente'])->name('cliente.dashboard');
    
    // Dashboard Repartidor
    Route::get('/repartidor/dashboard', [\App\Http\Controllers\RepartidorController::class, 'dashboard'])->name('repartidor.dashboard');
    
    // Dashboard Restaurante
    Route::get('/restaurante/dashboard', [\App\Http\Controllers\Restaurante\ProductoController::class, 'dashboardRestaurante'])->name('restaurante.dashboard');
    // Rutas para gestión de productos del restaurante
    Route::get('/restaurante/productos', [\App\Http\Controllers\Restaurante\ProductoController::class, 'index'])->name('productos.index');
    Route::post('/restaurante/productos', [\App\Http\Controllers\Restaurante\ProductoController::class, 'store'])->name('productos.store');
    Route::get('/restaurante/productos/{producto}/edit', [\App\Http\Controllers\Restaurante\ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/restaurante/productos/{producto}', [\App\Http\Controllers\Restaurante\ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/restaurante/productos/{producto}', [\App\Http\Controllers\Restaurante\ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::patch('/restaurante/productos/{producto}/toggle', [\App\Http\Controllers\Restaurante\ProductoController::class, 'toggleDisponibilidad'])->name('productos.toggle');

    // ===== RUTAS ADMINISTRATIVAS =====
    Route::get('/admin/users', [\App\Http\Controllers\Admin\AdminController::class, 'usuarios'])->name('admin.users');
    Route::post('/admin/users/{user}/role', [\App\Http\Controllers\Admin\AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
    // API para gestión desde modal (JSON)
    Route::get('/admin/api/users', [\App\Http\Controllers\Admin\AdminController::class, 'apiUsers'])->name('admin.api.users');
    Route::post('/admin/api/users/{user}/role', [\App\Http\Controllers\Admin\AdminController::class, 'apiUpdateUserRole'])->name('admin.api.users.updateRole');
    Route::delete('/admin/api/users/{user}', [\App\Http\Controllers\Admin\AdminController::class, 'apiDeleteUser'])->name('admin.api.users.delete');
    
    // Rutas administrativas adicionales
    Route::get('/admin/asignacion', [\App\Http\Controllers\Admin\AdminController::class, 'showAsignacion'])->name('admin.asignacion');
    Route::post('/admin/ordenes/{orden}/asignar', [\App\Http\Controllers\Admin\AdminController::class, 'asignarRepartidor'])->name('admin.ordenes.asignar');
    // Nueva ruta: crear una orden manualmente desde el modal y asignar repartidor al vuelo
    Route::post('/admin/ordenes/crear-asignar', [\App\Http\Controllers\Admin\AdminController::class, 'crearYAsignar'])->name('admin.ordenes.crearAsignar');
    // Permitir a admin cambiar estado de una orden (sin ser el restaurante)
    Route::post('/admin/ordenes/{orden}/estado', [\App\Http\Controllers\Admin\AdminController::class, 'cambiarEstadoAdmin'])->name('admin.ordenes.cambiarEstado');
    
    // Rutas para gestión de restaurantes (solo admin)
    Route::get('/admin/restaurantes', [\App\Http\Controllers\Admin\RestauranteController::class, 'index'])->name('admin.restaurantes');
    Route::post('/admin/restaurantes', [\App\Http\Controllers\Admin\RestauranteController::class, 'store'])->name('admin.restaurantes.store');
    Route::get('/admin/restaurantes/{restaurante}/edit', [\App\Http\Controllers\Admin\RestauranteController::class, 'edit'])->name('admin.restaurantes.edit');
    Route::put('/admin/restaurantes/{restaurante}', [\App\Http\Controllers\Admin\RestauranteController::class, 'update'])->name('admin.restaurantes.update');
    Route::delete('/admin/restaurantes/{restaurante}', [\App\Http\Controllers\Admin\RestauranteController::class, 'destroy'])->name('admin.restaurantes.destroy');
    
    // Rutas para gestión de productos (solo admin)
    Route::get('/admin/productos', [\App\Http\Controllers\Admin\ProductoController::class, 'index'])->name('admin.productos');
    Route::post('/admin/productos', [\App\Http\Controllers\Admin\ProductoController::class, 'store'])->name('admin.productos.store');
    Route::get('/admin/productos/{producto}/edit', [\App\Http\Controllers\Admin\ProductoController::class, 'edit'])->name('admin.productos.edit');
    Route::put('/admin/productos/{producto}', [\App\Http\Controllers\Admin\ProductoController::class, 'update'])->name('admin.productos.update');
    Route::delete('/admin/productos/{producto}', [\App\Http\Controllers\Admin\ProductoController::class, 'destroy'])->name('admin.productos.destroy');
    Route::patch('/admin/productos/{producto}/toggle', [\App\Http\Controllers\Admin\ProductoController::class, 'toggleDisponibilidad'])->name('admin.productos.toggle');
    
    // Rutas para cliente: crear orden (vista y envío)
    Route::get('cliente/orden/create', function () {
        // Mostrar lista de restaurantes para seleccionar
        $restaurantes = \App\Models\Restaurante::all(['id', 'nombre', 'descripcion', 'direccion']);
        return view('cliente.seleccionar_restaurante', ['restaurantes' => $restaurantes]);
    })->name('cliente.orden.create');

    // Ruta para crear orden con restaurante específico
    Route::get('cliente/orden/create/{restaurante}', function (\App\Models\Restaurante $restaurante) {
        // Cargar solo los productos del restaurante seleccionado
        $products = $restaurante->productos()->where('disponible', true)->get(['id','nombre','precio','descripcion']);
        return view('cliente.crear_orden', ['products' => $products, 'restaurante' => $restaurante]);
    })->name('cliente.orden.create.restaurante');

    // Endpoint AJAX para obtener productos por restaurante
    Route::get('cliente/orden/productos/{restaurante}', function (\App\Models\Restaurante $restaurante) {
        $productos = $restaurante->productos()->where('disponible', true)->get(['id','nombre','precio', 'descripcion']);
        return response()->json(['productos' => $productos]);
    })->name('cliente.orden.productos');

    Route::post('cliente/orden', [\App\Http\Controllers\Cliente\OrdenController::class, 'store'])
        ->name('cliente.orden.store');

    // Rutas para historial y tracking de órdenes del cliente
    Route::get('cliente/ordenes', [\App\Http\Controllers\Cliente\OrdenController::class, 'index'])
        ->name('cliente.ordenes.index');
    Route::get('cliente/ordenes/{orden}', [\App\Http\Controllers\Cliente\OrdenController::class, 'show'])
        ->name('cliente.ordenes.show');

    // Rutas para Ratings (calificaciones) - clientes autenticados
    Route::post('/ratings', [\App\Http\Controllers\RatingController::class, 'store'])
        ->name('ratings.store');

    // Rutas para repartidores
    Route::get('repartidor/ordenes', [\App\Http\Controllers\RepartidorController::class, 'misOrdenes'])
        ->name('repartidor.ordenes');
    Route::get('repartidor/ordenes/{orden}', [\App\Http\Controllers\RepartidorController::class, 'verOrden'])
        ->name('repartidor.ordenes.ver');
    Route::post('repartidor/ordenes/{orden}/estado', [\App\Http\Controllers\RepartidorController::class, 'actualizarEstado'])
        ->name('repartidor.ordenes.estado');
    Route::get('repartidor/historial', [\App\Http\Controllers\RepartidorController::class, 'historial'])
        ->name('repartidor.historial');

    // Rutas para pagos (placeholder)
    Route::get('pago/checkout', [\App\Http\Controllers\PagoController::class, 'checkout'])
        ->name('pago.checkout');
    Route::post('pago/procesar', [\App\Http\Controllers\PagoController::class, 'procesar'])
        ->name('pago.procesar');
    Route::get('pago/confirmacion/{transaccion_id}', [\App\Http\Controllers\PagoController::class, 'confirmacion'])
        ->name('pago.confirmacion');

    // Rutas para gestión de productos del restaurante
    Route::get('restaurante/productos', [\App\Http\Controllers\Restaurante\ProductoController::class, 'index'])->name('productos.index');
    Route::post('restaurante/productos', [\App\Http\Controllers\Restaurante\ProductoController::class, 'store'])->name('productos.store');
    // Acceder por id redirige a edición (evita 404 cuando se visita /restaurante/productos/{id})
    Route::get('restaurante/productos/{producto}', function (\App\Models\Producto $producto) {
        return redirect()->route('productos.edit', $producto);
    })->name('productos.show');
    Route::put('restaurante/productos/{producto}', [\App\Http\Controllers\Restaurante\ProductoController::class, 'update'])->name('productos.update');
    Route::delete('restaurante/productos/{producto}', [\App\Http\Controllers\Restaurante\ProductoController::class, 'destroy'])->name('productos.destroy');

    // Rutas para que el restaurante vea órdenes pendientes
    Route::get('restaurante/ordenes/pendientes', [\App\Http\Controllers\Cliente\OrdenController::class, 'pendientes'])->name('restaurante.ordenes.pendientes');
    // Ruta para cambiar estado de una orden (restaurante)
    Route::post('restaurante/ordenes/{orden}/estado', [\App\Http\Controllers\Cliente\OrdenController::class, 'cambiarEstado'])->name('restaurante.ordenes.cambiarEstado');
});

require __DIR__.'/auth.php';
