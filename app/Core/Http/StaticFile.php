<?php

namespace App\Core\Http;

class StaticFile
{
    private const MIME_TYPES = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'webmanifest' => 'application/manifest+json; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'webp' => 'image/webp',
        'txt' => 'text/plain; charset=utf-8',
        'xml' => 'application/xml; charset=utf-8',
    ];

    private const PUBLIC_PREFIXES = [
        '/assets/',
        '/manifest.webmanifest',
        '/robots.txt',
        '/sitemap.xml',
    ];

    public static function serveIfPublic(string $publicRoot): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        foreach (self::candidatePaths($path) as $candidate) {
            if (!self::isPublicPath($candidate)) {
                continue;
            }

            $file = realpath(rtrim($publicRoot, '/\\') . str_replace('/', DIRECTORY_SEPARATOR, $candidate));
            $root = realpath($publicRoot);

            if ($file && $root && strpos($file, $root) === 0 && is_file($file)) {
                self::stream($file);
            }
        }
    }

    private static function candidatePaths(string $path): array
    {
        $paths = [$path];
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $scriptDir = preg_replace('~/public$~', '', $scriptDir) ?: '';

        if ($scriptDir !== '' && strpos($path, $scriptDir . '/') === 0) {
            $paths[] = substr($path, strlen($scriptDir));
        }

        $normalized = [];
        foreach ($paths as $candidate) {
            $candidate = '/' . ltrim($candidate, '/');
            if (strpos($candidate, "\0") === false) {
                $normalized[] = $candidate;
            }
        }

        return array_values(array_unique($normalized));
    }

    private static function isPublicPath(string $path): bool
    {
        foreach (self::PUBLIC_PREFIXES as $prefix) {
            if ($path === $prefix || strpos($path, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function stream(string $file): void
    {
        if (headers_sent()) {
            readfile($file);
            exit;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        header('Content-Type: ' . (self::MIME_TYPES[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: public, max-age=604800');
        header('X-Content-Type-Options: nosniff');
        readfile($file);
        exit;
    }
}
