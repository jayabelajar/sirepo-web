<?php
    $siteName = $app_name ?? 'Sistem Informasi Repository Institut Agama Islam Hasan Jufri';
    $baseHref = $base_url ?? '';
    $pageTitle = $page_title ?? $siteName;
    $pageDescription = $page_description ?? '';
    $pageKeywords = $page_keywords ?? '';
    $canonical = $canonicalUrl ?? ($baseHref . ($_SERVER['REQUEST_URI'] ?? ''));
    $robots = $robotsDirective ?? 'index,follow';
    $ogType = $ogType ?? 'website';
    $ogImage = $ogImage ?? ($baseHref . '/assets/img/inhafi.png');
    $ogImageAlt = $ogImageAlt ?? $siteName;
    $cssHref = $cssHref ?? ($baseHref . '/assets/css/tailwind.css');
?>
<head>
    <meta charset="UTF-8" />
    <title><?= e($pageTitle); ?></title>
    <meta name="description" content="<?= e($pageDescription); ?>">
    <meta name="keywords" content="<?= e($pageKeywords); ?>">
    <meta name="application-name" content="<?= e($siteName); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="canonical" href="<?= e($canonical); ?>" />
    <meta name="robots" content="<?= e($robots); ?>">
    <meta name="author" content="<?= e($siteName); ?>">
    <meta name="theme-color" content="#059669">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="format-detection" content="telephone=no">

    <meta property="og:title" content="<?= e($pageTitle); ?>">
    <meta property="og:description" content="<?= e($pageDescription); ?>">
    <meta property="og:type" content="<?= e($ogType); ?>">
    <meta property="og:url" content="<?= e($canonical); ?>">
    <meta property="og:image" content="<?= e($ogImage); ?>">
    <meta property="og:image:alt" content="<?= e($ogImageAlt); ?>">
    <meta property="og:site_name" content="<?= e($siteName); ?>">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle); ?>">
    <meta name="twitter:description" content="<?= e($pageDescription); ?>">
    <meta name="twitter:image" content="<?= e($ogImage); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= $baseHref; ?>/assets/img/inhafi.png">
    <link rel="icon" type="image/svg+xml" href="<?= $baseHref; ?>/assets/img/favicon.svg">
    <link rel="alternate icon" href="<?= $baseHref; ?>/assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?= $baseHref; ?>/assets/img/apple-touch-icon.png">
    <link rel="manifest" href="<?= $baseHref; ?>/manifest.webmanifest">

    <link rel="stylesheet" href="<?= e($cssHref); ?>" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root { color-scheme: light; }
    </style>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": <?= json_encode($siteName); ?>,
      "url": <?= json_encode(rtrim($baseHref, '/')); ?>,
      "mainEntityOfPage": <?= json_encode($canonical); ?>,
      "description": <?= json_encode(strip_tags($pageDescription)); ?>,
      "inLanguage": "id-ID",
      "publisher": {
        "@type": "CollegeOrUniversity",
        "name": "Institut Agama Islam Hasan Jufri",
        "url": "https://inhafi.ac.id"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": <?= json_encode(rtrim($baseHref, '/') . '/telusuri?q={search_term_string}'); ?>,
        "query-input": "required name=search_term_string"
      }
    }
    </script>
</head>
