<?php
// Marca como ejecutadas (no destructivo) las migraciones que existan en
// database/migrations pero no estén en la tabla `migrations`.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Scanning migration files...\n";
$migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
$fileNames = array_map(function ($f) { return basename($f, '.php'); }, $migrationFiles);

$existing = DB::table('migrations')->pluck('migration')->toArray();

$missing = array_values(array_diff($fileNames, $existing));

if (empty($missing)) {
    echo "No hay migraciones pendientes para marcar.\n";
    exit(0);
}

$maxBatch = DB::table('migrations')->max('batch') ?? 0;
$batch = $maxBatch + 1;

foreach ($missing as $m) {
    DB::table('migrations')->insert([
        'migration' => $m,
        'batch' => $batch,
    ]);
    echo "Marked migration as run: $m (batch $batch)\n";
}

echo "Done. Inserted " . count($missing) . " migration(s).\n";
