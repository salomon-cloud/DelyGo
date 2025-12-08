<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

$base = 'http://127.0.0.1:8000';
$client = new Client(['base_uri' => $base, 'cookies' => true]);
$jar = new CookieJar();

try {
    // Get login page
    $res = $client->request('GET', '/login', ['cookies' => $jar]);
    $body = (string) $res->getBody();
    // extract csrf token
    if (preg_match('/name="_token" value="([^"]+)"/', $body, $m)) {
        $token = $m[1];
    } else {
        echo "No CSRF token found\n";
        exit(1);
    }

    // Post login
    $email = 'admin@example.com';
    $password = 'secret123';

    $res2 = $client->request('POST', '/login', [
        'cookies' => $jar,
        'form_params' => [
            '_token' => $token,
            'email' => $email,
            'password' => $password,
        ],
        'allow_redirects' => true
    ]);

    echo "Login POST status: " . $res2->getStatusCode() . "\n";
    $finalBody = (string) $res2->getBody();
    // Simple heuristic: if dashboard text exists
    if (stripos($finalBody, 'dashboard') !== false || stripos($finalBody, 'Cerrar sesión') !== false || stripos($finalBody, 'logout') !== false) {
        echo "Login appears successful (dashboard found)\n";
    } else {
        echo "Login response length: " . strlen($finalBody) . "\n";
        echo "First 500 chars:\n" . substr($finalBody,0,500) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
