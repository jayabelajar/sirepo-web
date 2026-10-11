<?php

use App\Core\App;
use App\Core\Http\StaticFile;
use App\Core\Security\Headers;

$envFile = __DIR__ . '/../.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $k = trim($k);
        $v = trim($v);
        if ($v !== '' && ($v[0] === '"' || $v[0] === "'")) {
            $v = trim($v, '\'"');
        }
        putenv("$k=$v");
        $_ENV[$k] = $v;
    }
}

$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base = __DIR__ . '/../app/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;

    $relative = substr($class, strlen($prefix));
    $file     = $base . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) require_once $file;
});

StaticFile::serveIfPublic(__DIR__);

$isApiRequest = strpos(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/api') === 0;
Headers::apply($isApiRequest);
if ($isApiRequest) {
    $corsOrigins = array_filter(array_map('trim', explode(',', (string) ($_ENV['API_CORS_ORIGINS'] ?? getenv('API_CORS_ORIGINS') ?: '*'))));
    Headers::applyCors($corsOrigins ?: ['*']);
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

require_once __DIR__ . '/../config/routes.php';

// helpers global
if (file_exists(__DIR__ . '/../app/Core/helpers.php')) {
    require_once __DIR__ . '/../app/Core/helpers.php';
}

$app = new App();
$app->run();
