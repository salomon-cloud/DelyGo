<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

function attempt($email, $password) {
    $guard = Auth::guard('web');
    $ok = Auth::attempt(['email' => $email, 'password' => $password]);
    echo "Attempt $email -> ".($ok?"SUCCESS":"FAIL")."\n";
}

attempt('admin@example.com','secret123');
attempt('restowner1@example.com','secret123');
attempt('cliente1@example.com','secret123');
