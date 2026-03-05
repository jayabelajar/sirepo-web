<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Models\Repository;

class HomeController extends Controller
{
    public function index()
    {
        $repo   = new Repository();
        $latest = $repo->getLatest(6);

        $seo = [
            'title'       => 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri',
            'description' => 'Repositori akademik resmi Institut Agama Islam Hasan Jufri untuk publikasi dan rujukan ilmiah.'
        ];

        $this->view('public/home', compact('seo', 'latest'));
    }
}
