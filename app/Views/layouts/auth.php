<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? ($app_name ?? 'Auth') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= $base_url; ?>/assets/img/inhafi.png">
    <link rel="icon" type="image/svg+xml" href="<?= $base_url; ?>/assets/img/favicon.svg">
    <link rel="alternate icon" href="<?= $base_url; ?>/assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?= $base_url; ?>/assets/img/apple-touch-icon.png">
    <link rel="manifest" href="<?= $base_url; ?>/manifest.webmanifest">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">
    <main class="flex-1 w-full flex items-center justify-center p-4 sm:p-6">
        <?= $content ?>
    </main>
</body>
</html>
