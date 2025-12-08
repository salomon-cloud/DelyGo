<?php
// Inserta 3 repartidores y 3 restaurantes de ejemplo si no existen.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Restaurante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

echo "Seeding demo repartidores y restaurantes (idempotente)...\n";
// Ensure we have a working DB connection. If default DB is down, fall back to sqlite local file.
try {
    DB::connection()->getPdo();
} catch (\Exception $e) {
    echo "DB connection failed: switching to sqlite fallback (database/database.sqlite)\n";
    $sqlitePath = __DIR__ . '/../database/database.sqlite';
    if (! file_exists($sqlitePath)) {
        file_put_contents($sqlitePath, '');
        echo "Created sqlite database file at: {$sqlitePath}\n";
    }
    Config::set('database.default', 'sqlite');
    Config::set('database.connections.sqlite.database', $sqlitePath);
    // Purge and reconnect
    DB::purge('sqlite');
    DB::reconnect('sqlite');
    // Run migrations automatically on sqlite fallback so tables exist for the seed script
    try {
        echo "Running migrations on sqlite fallback...\n";
        $kernel->call('migrate', ['--force' => true]);
        echo "Migrations completed.\n";
    } catch (\Exception $e) {
        echo "Migrations failed: " . $e->getMessage() . "\n";
    }
}

// Repartidores (3)
for ($i = 1; $i <= 3; $i++) {
    $email = "seed-repartidor+$i@example.local";
    $user = User::where('email', $email)->first();
    if (! $user) {
        $user = User::create([
            'name' => "Repartidor Demo $i",
            'email' => $email,
            'password' => bcrypt('secret'),
            'rol' => 'repartidor',
        ]);
        echo "Creado repartidor: {$user->id} {$user->email}\n";
    } else {
        echo "Repartidor ya existe: {$user->id} {$user->email}\n";
    }
}

// Restaurantes (3) - cada restaurante necesita user_id referencing a user with rol 'restaurante'
for ($i = 1; $i <= 3; $i++) {
    $restName = "Restaurante Demo $i";
    $exists = Restaurante::where('nombre', $restName)->first();
    if (! $exists) {
        // Create a backing user for the restaurante role
        $email = "seed-restaurante+$i@example.local";
        $user = User::where('email', $email)->first();
        if (! $user) {
            $user = User::create([
                'name' => "User Restaurante $i",
                'email' => $email,
                'password' => bcrypt('secret'),
                'rol' => 'restaurante',
            ]);
            echo "Creado user para restaurante: {$user->id} {$user->email}\n";
        }

        $rest = new Restaurante();
        // Ensure restaurante references the created user when the column exists
        if (Schema::hasColumn('restaurantes', 'user_id')) {
            $rest->user_id = $user->id;
        }
        $rest->nombre = $restName;
        $rest->direccion = 'Calle Demo 123';
        $rest->save();

        echo "Creado restaurante: {$rest->id} {$rest->nombre}\n";
    } else {
        echo "Restaurante ya existe: {$exists->id} {$exists->nombre}\n";
    }
}

echo "Seed demo completado.\n";
