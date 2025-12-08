<?php
// Test: bootstrap app, login as admin via auth()->loginUsingId(), create or find a demo order, and call AdminController::asignarRepartidor directly.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Orden;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;

$admin = User::where('rol', 'admin')->first();
if (! $admin) {
    echo "No admin user found. Run scripts/create_admin_demo.php first.\n";
    exit(1);
}

// Login as admin (no HTTP session, but auth facade will have user)
auth()->loginUsingId($admin->id);

// Find an order in 'preparando' without repartidor
$orden = Orden::where('estado', 'preparando')->whereNull('repartidor_id')->first();
if (! $orden) {
    echo "No suitable order found. Run scripts/create_demo_order.php first.\n";
    exit(1);
}

// Choose a repartidor: take first with role 'repartidor'
$repartidor = User::where('rol', 'repartidor')->first();
if (! $repartidor) {
    echo "No repartidor found. Run scripts/seed_asignacion_demo.php first.\n";
    exit(1);
}

$controller = new AdminController();

// Build a Request object similar to HTTP POST
$req = Request::create('/admin/ordenes/'.$orden->id.'/asignar', 'POST', [
    'repartidor_id' => $repartidor->id,
]);

$response = $controller->asignarRepartidor($req, $orden);

// The controller returns a JsonResponse. Print its content and the updated order.
if (method_exists($response, 'getContent')) {
    echo "Controller response: \n" . $response->getContent() . "\n";
}

$orden->refresh();
echo "Order after assignment: id={$orden->id} repartidor_id={$orden->repartidor_id} estado={$orden->estado}\n";
