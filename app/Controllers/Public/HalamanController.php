<?php

namespace App\Controllers\Public;

use App\Core\Controller;

class HalamanController extends Controller
{
    private function seo(string $pageTitle, string $path, string $description): array
    {
        $config  = require __DIR__ . '/../../../config/config.php';
        $appName = $config['app_name'] ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri';
        $base    = rtrim($config['base_url'] ?? '', '/');

        return [
            'title'       => $pageTitle . ' | ' . $appName,
            'description' => $description,
            'canonical'   => $base . $path,
        ];
    }

    public function faq()
    {
        $seo = $this->seo('FAQ', '/faq', 'Pertanyaan umum terkait penggunaan layanan repositori.');
        $this->view('public/faq', compact('seo'));
    }

    public function panduan()
    {
        $seo = $this->seo('Panduan Pengguna', '/panduan', 'Panduan ringkas penggunaan layanan repositori.');
        $this->view('public/panduan', compact('seo'));
    }

    public function privasi()
    {
        $seo = $this->seo('Kebijakan Privasi', '/kebijakan', 'Kebijakan pengelolaan data pengguna dan dokumen repositori.');
        $this->view('public/kebijakan', compact('seo'));
    }

    public function ketentuan()
    {
        $seo = $this->seo('Syarat & Ketentuan', '/syarat', 'Ketentuan penggunaan layanan repositori akademik.');
        $this->view('public/syarat', compact('seo'));
    }

    public function kontak()
    {
        $seo = $this->seo('Kontak', '/kontak', 'Kontak resmi pengelola repositori untuk informasi layanan.');
        $this->view('public/kontak', compact('seo'));
    }
}
