<?php
    $baseHref = rtrim($base_url ?? '', '/');
    $navLinks = $navLinks ?? [];
    $ctaButtons = $ctaButtons ?? [];
    $brand = array_merge([
        'tagline'   => 'Sistem Informasi Repositori',
        'subtitle'  => 'Institut Agama Islam Hasan Jufri',
        'logo_alt'  => 'Logo Sistem Informasi Repository',
        'logo_path' => '/assets/img/favicon.svg',
    ], $brand ?? []);
    $brandTaglineUpper = strtoupper((string) ($brand['tagline'] ?? ''));
    $brandSubtitleUpper = strtoupper((string) ($brand['subtitle'] ?? ''));

    $buildUrl = static function (string $path) use ($baseHref): string {
        if (preg_match('~^https?://~', $path)) {
            return $path;
        }
        $normalized = '/' . ltrim($path, '/');
        return $baseHref . $normalized;
    };

    $renderIcon = static function (string $name, string $classes): string {
        switch ($name) {
            case 'download':
                return '<svg class="' . e($classes) . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>';
            case 'search':
            default:
                return '<svg class="' . e($classes) . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>';
        }
    };
?>
<header class="sticky top-0 z-40 border-b border-slate-100 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
            <a href="<?= $buildUrl('/'); ?>" class="flex min-w-0 items-center gap-2 pr-2 sm:gap-4">
                <div class="w-10 h-10 sm:w-14 sm:h-14 flex-shrink-0 flex items-center justify-center">
                    <img src="<?= $baseHref . $brand['logo_path']; ?>" alt="<?= e($brand['logo_alt']); ?>" class="w-full h-full object-contain" />
                </div>
                <div class="min-w-0 leading-tight">
                    <p class="text-[10px] md:text-xs uppercase tracking-wide text-slate-500 font-bold">
                        <?= e($brandTaglineUpper); ?>
                    </p>
                    <p class="text-[11px] sm:text-sm md:text-base font-bold text-slate-900 leading-tight">
                        <span class="text-emerald-600"><?= e($brandSubtitleUpper); ?></span>
                    </p>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-8">
                <div class="flex items-center gap-7">
                    <?php foreach ($navLinks as $link): ?>
                        <a href="<?= $buildUrl($link['path'] ?? '/'); ?>" class="text-base font-semibold text-slate-700 hover:text-emerald-600 transition-colors">
                            <?= e($link['label'] ?? 'Menu'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="flex items-center gap-4">
                    <?php foreach ($ctaButtons as $button): ?>
                        <?php
                            $variant = $button['variant'] ?? 'outline';
                            $desktopLabel = $button['label'] ?? '';
                            $classes = $variant === 'solid'
                                ? 'inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-bold shadow-md shadow-emerald-500/30 hover:bg-emerald-700 transition-all active:scale-95'
                                : 'inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-500 text-emerald-600 text-sm font-bold hover:bg-emerald-50 transition-colors active:scale-95';
                        ?>
                        <a href="<?= $buildUrl($button['path'] ?? '/'); ?>" class="<?= $classes; ?>">
                            <?= $renderIcon($button['icon'] ?? 'search', 'w-4 h-4'); ?>
                            <?= e($desktopLabel); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </nav>

            <div class="flex items-center gap-1 lg:hidden">
                <button id="menuBtn" class="p-2 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors" aria-label="Menu">
                    <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="lg:hidden hidden border-t border-slate-100 bg-white shadow-lg">
        <div class="px-6 py-4 space-y-2 text-base font-medium">
            <?php foreach ($navLinks as $link): ?>
                <a href="<?= $buildUrl($link['path'] ?? '/'); ?>" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                    <?= e($link['label'] ?? 'Menu'); ?>
                </a>
            <?php endforeach; ?>

            <?php foreach ($ctaButtons as $button): ?>
                <?php
                    $variant = $button['variant'] ?? 'outline';
                    $mobileLabel = $button['mobile_label'] ?? $button['label'] ?? '';
                    $classes = $variant === 'solid'
                        ? 'flex items-center justify-center gap-2 mt-2 px-4 py-3 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors'
                        : 'flex items-center justify-center gap-2 mt-4 px-4 py-3 rounded-lg border border-emerald-500 text-emerald-600 font-bold hover:bg-emerald-50 transition-colors';
                ?>
                <a href="<?= $buildUrl($button['path'] ?? '/'); ?>" class="<?= $classes; ?>">
                    <?= $renderIcon($button['icon'] ?? 'search', 'w-5 h-5'); ?>
                    <?= e($mobileLabel); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
