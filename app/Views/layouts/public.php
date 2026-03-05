<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <?php
        $requestUri      = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath     = parse_url($requestUri, PHP_URL_PATH) ?: '/';
        $requestQuery    = parse_url($requestUri, PHP_URL_QUERY);
        $basePath        = rtrim(parse_url($base_url ?? '', PHP_URL_PATH) ?? '', '/');
        if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }
        if ($requestPath === '' || $requestPath === false) {
            $requestPath = '/';
        }
        $currentUrl      = rtrim($base_url ?? '', '/') . $requestPath . ($requestQuery ? ('?' . $requestQuery) : '');
        $canonicalUrl    = $seo['canonical'] ?? $currentUrl;
        $robotsDirective = $seo['robots'] ?? 'index,follow';

        $page_title       = e($seo['title'] ?? $app_name);
        $page_description = e($seo['description'] ?? 'Repositori akademik Institut Agama Islam Hasan Jufri untuk publikasi ilmiah terkurasi.');
        $page_keywords    = e($seo['keywords'] ?? 'repository, Institut Agama Islam Hasan Jufri, sirepo, skripsi, tesis, tugas akhir, penelitian');

        $ogType      = $seo['og_type'] ?? 'website';
        $ogImage     = $seo['image'] ?? (rtrim($base_url ?? '', '/') . '/assets/img/inhafi.png');
        $ogImageAlt  = $seo['image_alt'] ?? ($seo['title'] ?? $app_name);

        // Template data untuk footer agar rapi dan mudah dirawat.
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
    ?>
    <title><?= $page_title; ?></title>
    <meta name="description" content="<?= $page_description; ?>">
    <meta name="keywords" content="<?= $page_keywords; ?>">
    <meta name="application-name" content="<?= e($app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="canonical" href="<?= e($canonicalUrl); ?>" />
    <meta name="robots" content="<?= e($robotsDirective); ?>">
    <meta name="author" content="<?= e($app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri'); ?>">
    <meta name="theme-color" content="#059669">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="format-detection" content="telephone=no">

    <meta property="og:title" content="<?= $page_title; ?>">
    <meta property="og:description" content="<?= $page_description; ?>">
    <meta property="og:type" content="<?= e($ogType); ?>">
    <meta property="og:url" content="<?= e($canonicalUrl); ?>">
    <meta property="og:image" content="<?= e($ogImage); ?>">
    <meta property="og:image:alt" content="<?= e($ogImageAlt); ?>">
    <meta property="og:site_name" content="<?= e($app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri'); ?>">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $page_title; ?>">
    <meta name="twitter:description" content="<?= $page_description; ?>">
    <meta name="twitter:image" content="<?= e($ogImage); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= $base_url; ?>/assets/img/inhafi.png">
    <link rel="icon" type="image/svg+xml" href="<?= $base_url; ?>/assets/img/favicon.svg">
    <link rel="alternate icon" href="<?= $base_url; ?>/assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?= $base_url; ?>/assets/img/apple-touch-icon.png">
    <link rel="manifest" href="<?= $base_url; ?>/manifest.webmanifest">
    
    <?php
      $cssPath = __DIR__ . '/../../../public/assets/css/tailwind.css';
      $cssVersion = file_exists($cssPath) ? filemtime($cssPath) : time();
    ?>
    <link rel="stylesheet" href="<?= $base_url; ?>/assets/css/tailwind.css?v=<?= $cssVersion ?>" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root { color-scheme: light; }
    </style>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": <?= json_encode($app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri'); ?>,
      "url": <?= json_encode(rtrim($base_url, '/')); ?>,
      "mainEntityOfPage": <?= json_encode($canonicalUrl); ?>,
      "description": <?= json_encode($page_description); ?>,
      "inLanguage": "id-ID",
      "publisher": {
        "@type": "CollegeOrUniversity",
        "name": "Institut Agama Islam Hasan Jufri",
        "url": "https://inhafi.ac.id"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": <?= json_encode(rtrim($base_url, '/') . '/telusuri?q={search_term_string}'); ?>,
        "query-input": "required name=search_term_string"
      }
    }
    </script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

<header class="sticky top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between">

        <a href="<?= $base_url; ?>/" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-emerald-500/30 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.247m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.247"></path></svg>
            </div>
            <div class="leading-tight">
                <p class="text-base font-bold text-slate-900 tracking-tight hidden sm:block">SIREPO INHAFI</p>
                <p class="text-xs text-slate-500 font-medium tracking-wide hidden sm:block">Repositori Akademik IAI Hasan Jufri</p>
                <p class="text-lg font-bold tracking-tight sm:hidden text-slate-900">
                    SIREPO <span class="text-emerald-600">INHAFI</span>
                </p>
            </div>
        </a>

        <nav class="hidden lg:flex items-center gap-8">
            <!-- MENU TEXT -->
            <div class="flex items-center gap-7">
                <a href="<?= $base_url; ?>/" class="text-base font-semibold text-slate-700 hover:text-emerald-600 transition-colors">Beranda</a>
                <a href="<?= $base_url; ?>/tentang" class="text-base font-semibold text-slate-700 hover:text-emerald-600 transition-colors">Tentang</a>
                <a href="<?= $base_url; ?>/kontak" class="text-base font-semibold text-slate-700 hover:text-emerald-600 transition-colors">Kontak</a>
            </div>

            <!-- ACTION BUTTON -->
            <div class="flex items-center gap-4">
                <a href="<?= $base_url; ?>/telusuri/"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-500 text-emerald-600 text-sm font-bold hover:bg-emerald-50 transition-colors active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Telusuri
                </a>

                    <a href="<?= $base_url; ?>/download"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-bold shadow-md shadow-emerald-500/30 hover:bg-emerald-700 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Aplikasi
                    </a>
                </div>
            </nav>

          <div class="flex items-center gap-1 lg:hidden">
              <!-- SEARCH ICON (mobile) -->
              <a href="<?= $base_url; ?>/telusuri/"
                class="p-2 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors"
                aria-label="Cari Repository">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
              </a>

              <!-- HAMBURGER (mobile) -->
              <button id="menuBtn" class="p-2 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors" aria-label="Menu">
                  <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                      viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
              </button>
          </div>

    </div>

    <div id="mobileMenu" class="lg:hidden hidden border-t border-slate-100 bg-white shadow-lg">
        <div class="px-6 py-4 space-y-2 text-base font-medium">
            <a href="<?= $base_url; ?>/" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">Beranda</a>
            <a href="<?= $base_url; ?>/tentang" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">Tentang</a>
            <a href="<?= $base_url; ?>/kontak" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">Kontak</a>

            <a href="<?= $base_url; ?>/telusuri"
               class="flex items-center justify-center gap-2 mt-4 px-4 py-3 rounded-lg border border-emerald-500 text-emerald-600 font-bold hover:bg-emerald-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Telusuri Dokumen
            </a>
            
            <a href="<?= $base_url; ?>/download"
               class="flex items-center justify-center gap-2 mt-2 px-4 py-3 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh Aplikasi
            </a>
        </div>
    </div>
</header>

<main class="min-h-[60vh] pt-8 pb-10"> 
    <?= $content ?? '' ?>
</main>

<footer class="border-t border-slate-100 bg-white mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 lg:py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 sm:gap-5 lg:gap-6 text-sm text-slate-600">
            <section class="lg:col-span-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.247m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.247"></path></svg>
                    </div>
                    <p class="text-lg font-bold text-slate-900 tracking-tight">SIREPO INHAFI</p>
                </div>
                <p class="text-xs sm:text-sm leading-relaxed text-slate-700">
                    Repositori akademik resmi Institut Agama Islam Hasan Jufri untuk publikasi, preservasi, dan diseminasi karya ilmiah sivitas akademika.
                </p>
            </section>

            <?php foreach ($footerLinkGroups as $group): ?>
                <section class="lg:col-span-2 rounded-2xl border border-slate-100 bg-slate-50/50 p-4 sm:p-5">
                    <p class="font-bold text-slate-900 text-sm mb-3"><?= e($group['title']); ?></p>
                    <ul class="space-y-2 text-sm">
                        <?php foreach ($group['links'] as $item): ?>
                            <li>
                                <a href="<?= $base_url . $item['path']; ?>" class="text-slate-600 hover:text-emerald-700 transition-colors">
                                    <?= e($item['label']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>

            <section class="lg:col-span-4 rounded-2xl border border-slate-100 bg-slate-50/50 p-4 sm:p-5">
                <p class="font-bold text-slate-900 text-sm mb-3">Kontak Resmi</p>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.898a2 2 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Jl. Kampus INHAFI, Lebak, Kec. Sangkapura, Kabupaten Gresik, Jawa Timur</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:info@inhafi.ac.id" class="hover:text-emerald-600 font-medium break-all">info@inhafi.ac.id</a>
                    </li>
                </ul>
            </section>
        </div>
    </div>

    <div class="border-t border-slate-100 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-center sm:text-left">
                &copy; <?= date('Y'); ?>
                <a href="https://inhafi.ac.id"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="font-medium text-slate-600 hover:text-emerald-600 transition-colors">
                    Institut Agama Islam Hasan Jufri
                </a>.
            </p>

            <p class="text-center sm:text-right font-medium text-slate-600">
                Pengembangan sistem:
                <a href="#"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="font-semibold text-slate-700 hover:text-emerald-600 transition-colors">
                    VeritasDev
                </a>
                <span class="px-1 text-slate-400">|</span>
                <a href="https://polije.ac.id"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="font-semibold text-slate-700 hover:text-emerald-600 transition-colors">
                    Politeknik Negeri Jember
                </a>
            </p>
        </div>
    </div>
</footer>

<script>
    // === MOBILE MENU TOGGLE LOGIC ===

    const menuBtn = document.getElementById('menuBtn');
    const menu = document.getElementById('mobileMenu');
    const menuIcon = document.getElementById('menuIcon');

    if (menuBtn && menu) {
        menuBtn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            if (menu.classList.contains('hidden')) {
                // Hamburger
                menuIcon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            } else {
                // Close (X)
                menuIcon.setAttribute('d', 'M6 18L18 6M6 6l12 12');
            }
        });
    }
</script>

</body>
</html>
