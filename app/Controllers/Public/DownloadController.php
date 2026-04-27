<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Models\Repository;

class DownloadController extends Controller
{
    public function index()
    {
        return $this->view('public/download', [
            'title' => 'Unduh Aplikasi Mobile - Repositori IAI Hasan Jufri',
            'download_link' => 'https://drive.google.com/drive/folders/1DabOv2j8KVBh3X3qPkOsc5zX-smbeQmk?usp=sharing'
        ]);
    }

    public function repository($slug)
    {
        $repo = (new Repository())->getBySlug($slug);
        if (!$repo || empty($repo['file_pdf'])) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        $baseDir = realpath(__DIR__ . '/../../../storage/uploads');
        if ($baseDir === false) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        $path = realpath($baseDir . '/' . $repo['file_pdf']);
        if (!$path || strpos($path, $baseDir) !== 0 || !is_file($path)) {
            http_response_code(404);
            exit('File tidak ditemukan.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($path) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}
