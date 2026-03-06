<?php
    $requestUri   = $_SERVER['REQUEST_URI'] ?? '/';
    $requestPath  = parse_url($requestUri, PHP_URL_PATH) ?: '/';
    $requestQuery = parse_url($requestUri, PHP_URL_QUERY);
    $basePath     = rtrim(parse_url($base_url ?? '', PHP_URL_PATH) ?? '', '/');
    if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
        $requestPath = substr($requestPath, strlen($basePath));
    }
    if ($requestPath === '' || $requestPath === false) {
        $requestPath = '/';
    }

    $basePublicUrl   = rtrim($base_url ?? 'http://localhost', '/');
    $currentUrl      = $basePublicUrl . $requestPath . ($requestQuery ? ('?' . $requestQuery) : '');
    $canonicalUrl    = $seo['canonical'] ?? $currentUrl;
    $robotsDirective = $seo['robots'] ?? 'index,follow';

    $pageTitle       = $seo['title'] ?? ($app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri');
    $pageDescription = $seo['description'] ?? 'Repositori akademik Institut Agama Islam Hasan Jufri untuk publikasi ilmiah terkurasi.';
    $pageKeywords    = $seo['keywords'] ?? 'repository, Institut Agama Islam Hasan Jufri, sirepo, skripsi, tesis, tugas akhir, penelitian';

    $ogType     = $seo['og_type'] ?? 'website';
    $ogImage    = $seo['image'] ?? ($basePublicUrl . '/assets/img/inhafi.png');
    $ogImageAlt = $seo['image_alt'] ?? $pageTitle;

    $footerLinkGroups = [
        [
            'title' => 'Akses Cepat',
            'links' => [
                ['label' => 'Beranda', 'path' => '/'],
                ['label' => 'Tentang Repository', 'path' => '/tentang'],
                ['label' => 'Kontak', 'path' => '/kontak'],
                ['label' => 'Unduh Aplikasi', 'path' => '/download'],
            ],
        ],
        [
            'title' => 'Informasi & Kebijakan',
            'links' => [
                ['label' => 'Panduan Pengguna', 'path' => '/panduan'],
                ['label' => 'Kebijakan Privasi', 'path' => '/kebijakan'],
                ['label' => 'Syarat Layanan', 'path' => '/syarat'],
                ['label' => 'FAQ', 'path' => '/faq'],
            ],
        ],
    ];

    $navLinks = [
        ['label' => 'Beranda', 'path' => '/'],
        ['label' => 'Tentang', 'path' => '/tentang'],
        ['label' => 'Kontak', 'path' => '/kontak'],
    ];

    $ctaButtons = [
        [
            'label'        => 'Telusuri',
            'mobile_label' => 'Telusuri Dokumen',
            'path'         => '/telusuri/',
            'variant'      => 'outline',
            'icon'         => 'search',
        ],
        [
            'label'        => 'Unduh Aplikasi',
            'mobile_label' => 'Unduh Aplikasi',
            'path'         => '/download',
            'variant'      => 'solid',
            'icon'         => 'download',
        ],
    ];

    $headProps = [
        'app_name'        => $app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri',
        'base_url'        => $basePublicUrl,
        'page_title'      => $pageTitle,
        'page_description'=> $pageDescription,
        'page_keywords'   => $pageKeywords,
        'canonicalUrl'    => $canonicalUrl,
        'robotsDirective' => $robotsDirective,
        'ogType'          => $ogType,
        'ogImage'         => $ogImage,
        'ogImageAlt'      => $ogImageAlt,
    ];
?>
<!DOCTYPE html>
<html lang="id">
<?php component('public/head', $headProps); ?>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

<?php component('public/header', [
    'base_url'   => $basePublicUrl,
    'navLinks'   => $navLinks,
    'ctaButtons' => $ctaButtons,
]); ?>

<main class="min-h-[60vh] pt-8 pb-10"> 
    <?= $content ?? '' ?>
</main>

<?php component('public/footer', [
    'base_url'         => $basePublicUrl,
    'footerLinkGroups' => $footerLinkGroups,
]); ?>

<?php component('public/scripts'); ?>

</body>
</html>

