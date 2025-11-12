<?php
if ($argc < 3) {
    echo "Usage: php reset_password.php user@example.com newpassword\n";
    exit(1);
}
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1];
$new = $argv[2];

$user = App\Models\User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(1);
}

$user->password = $new; // User model casts 'password' => 'hashed' so save will hash
$user->save();

echo "Password updated for $email\n";
