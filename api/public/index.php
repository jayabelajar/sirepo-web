<?php
// Front controller untuk subdomain API.
// Dialihkan ke aplikasi utama supaya logika tetap satu sumber.

$envFile = __DIR__ . '/../../.env';
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

require_once __DIR__ . '/../../app/Core/Security/Headers.php';

$corsOrigins = array_filter(array_map('trim', explode(',', (string) ($_ENV['API_CORS_ORIGINS'] ?? getenv('API_CORS_ORIGINS') ?: '*'))));
\App\Core\Security\Headers::apply(true);
\App\Core\Security\Headers::applyCors($corsOrigins ?: ['*']);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Block direct root access on the API subdomain, show simple generic 404 (no hints)
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
if ($reqPath === '' || $reqPath === '/') {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>404 Not Found</title>'
        . '<style>body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#0b1220;color:#d7e1ff;display:flex;align-items:center;justify-content:center;height:100vh;}'
        . '.card{background:#111a2e;border:1px solid #1f2a44;border-radius:10px;padding:32px;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.4);}h1{margin:0 0 12px;font-size:28px;}p{margin:0;line-height:1.5;color:#a9b7d9;}</style>'
        . '</head><body><div class="card"><h1>404</h1>'
        . '<p>Halaman tidak ditemukan.</p>'
        . '</div></body></html>';
    exit;
}

require __DIR__ . '/../../index.php';
