<?php

namespace App\Core\Security;

class Headers
{
    public static function apply(bool $isApi = false): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

        if (!$isApi) {
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'");
        }
    }

    public static function applyCors(array $allowedOrigins): void
    {
        if (headers_sent()) {
            return;
        }

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowAny = in_array('*', $allowedOrigins, true);
        $allowed = $allowAny ? '*' : ($origin && in_array($origin, $allowedOrigins, true) ? $origin : '');

        if ($allowed !== '') {
            header('Access-Control-Allow-Origin: ' . $allowed);
            if ($allowed !== '*') {
                header('Vary: Origin');
            }
        }

        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 600');
    }
}
