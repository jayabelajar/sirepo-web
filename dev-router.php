<?php

// Router for PHP built-in server:
// - Serve static files from /public directly
// - Forward everything else to the app front controller
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$publicFile = __DIR__ . '/public' . $requestPath;

if ($requestPath !== '/' && is_file($publicFile)) {
    return false;
}

require __DIR__ . '/public/index.php';
