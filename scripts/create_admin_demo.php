<?php
// Crea o actualiza un usuario admin con credenciales conocidas para pruebas.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'admin+demo@example.local';
$passwordPlain = 'secret';

$user = User::where('email', $email)->first();
if (! $user) {
    $user = User::create([
        'name' => 'Admin Demo',
        'email' => $email,
        'password' => bcrypt($passwordPlain),
        'rol' => 'admin',
    ]);
    echo "Created admin user: {$user->id} {$user->email}\n";
} else {
    $user->name = 'Admin Demo';
    $user->rol = 'admin';
    $user->password = bcrypt($passwordPlain);
    $user->save();
    echo "Updated admin user: {$user->id} {$user->email}\n";
}

echo "Admin credentials: email={$email}, password={$passwordPlain}\n";
