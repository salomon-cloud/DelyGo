<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;

$email = 'admin@example.com';
$user = App\Models\User::where('email', $email)->first();
if (! $user) {
    echo "NO USER with email $email\n";
    exit(0);
}

echo "User id: {$user->id}\n";
echo "Password column: {$user->password}\n";
echo (Hash::check('secret123', $user->password) ? "HASH OK\n" : "HASH BAD\n");
