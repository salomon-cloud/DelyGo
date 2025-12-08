<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$rows = DB::select('select id, cliente_id, restaurante_id, repartidor_id, estado, total, direccion_entrega, created_at from ordenes order by id desc limit 20');
foreach($rows as $r){
    echo json_encode($r) . PHP_EOL;
}
if(empty($rows)) echo "No orders found\n";
