<?php

use App\Core\ViewEngine;

function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

if (!function_exists('component')) {
    /**
     * Render a reusable view component stored under app/Views/components.
     */
    function component(string $name, array $data = []): void
    {
        ViewEngine::getInstance()->include($name, $data);
    }
}
