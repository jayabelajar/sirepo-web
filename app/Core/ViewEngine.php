<?php

namespace App\Core;

/**
 * Minimal view engine for rendering reusable view fragments/components.
 */
class ViewEngine
{
    private static ?self $instance = null;

    private string $componentDir;

    private function __construct()
    {
        $this->componentDir = realpath(__DIR__ . '/../Views/components') ?: (__DIR__ . '/../Views/components');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render(string $component, array $data = []): string
    {
        $path = $this->resolvePath($component);

        if (!is_file($path)) {
            throw new \RuntimeException("Component view '{$component}' tidak ditemukan ({$path})");
        }

        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        ob_start();
        include $path;
        return ob_get_clean();
    }

    public function include(string $component, array $data = []): void
    {
        echo $this->render($component, $data);
    }

    private function resolvePath(string $component): string
    {
        $clean = str_replace(['..', '\\'], ['', '/'], $component);
        $clean = ltrim($clean, '/');

        return rtrim($this->componentDir, '/\\') . '/' . $clean . '.php';
    }
}
