<?php

namespace App\Controllers\Public;

use App\Core\Controller;

class RobotsController extends Controller
{
    public function index(): void
    {
        $config = require __DIR__ . '/../../../config/config.php';
        $base = rtrim((string)($config['base_url'] ?? ''), '/');

        // Fallback aman saat base_url kosong.
        if ($base === '') {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            $scheme = $isHttps ? 'https' : 'http';
            $base = $scheme . '://' . $host;
        }

        header('Content-Type: text/plain; charset=utf-8');

        $lines = [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /developer/',
            'Disallow: /dosen/',
            'Disallow: /mahasiswa/',
            'Disallow: /api/',
            'Disallow: /assets/uploads/',
            'Disallow: /login/',
            'Disallow: /logout',
            'Disallow: /reset-password/',
            'Allow: /repository/',
            'Allow: /telusuri',
            'Allow: /sitemap.xml',
            '',
            'Sitemap: ' . $base . '/sitemap.xml',
        ];

        echo implode("\n", $lines) . "\n";
        exit;
    }
}
