<?php
    $baseHref = rtrim($base_url ?? '', '/');
    $footerLinkGroups = $footerLinkGroups ?? [];
?>
<footer class="mt-12 bg-slate-950 text-slate-300 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <section class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-6 flex flex-col h-full">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-0 blur-lg bg-emerald-400/40 rounded-full"></div>
                        <div class="relative w-12 h-12 rounded-2xl bg-white border border-emerald-100 flex items-center justify-center shadow-sm shadow-emerald-100 overflow-hidden">
                            <img src="<?= $baseHref; ?>/assets/img/image.png" alt="Logo Sistem Informasi Repository" class="w-10 h-10 object-contain" />
                        </div>
                    </div>
                    <div class="leading-tight">
                        <p class="text-base font-semibold tracking-wide text-white">SIREPO INHAFI</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-200/70">Institut Agama Islam Hasan Jufri</p>
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-slate-400">
                    Repositori digital yang memastikan karya ilmiah kampus terdokumentasi, terkurasi, dan mudah diakses secara aman.
                </p>
                <ul class="space-y-2 text-sm flex-1">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Jl. Kampus INHAFI, Lebak, Kec. Sangkapura, Gresik</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:info@inhafi.ac.id" class="font-medium text-white hover:text-emerald-300 transition-colors">info@inhafi.ac.id</a>
                    </li>
                </ul>
            </section>

            <section class="rounded-2xl border border-white/10 bg-white/5 p-6 h-full">
                <div class="grid grid-cols-2 gap-5 text-sm h-full">
                <?php foreach ($footerLinkGroups as $group): ?>
                    <div class="space-y-2">
                        <p class="font-semibold text-white text-sm mb-2 tracking-wide"><?= e($group['title'] ?? 'Menu'); ?></p>
                        <ul class="space-y-1.5">
                            <?php foreach ($group['links'] ?? [] as $item): ?>
                                <li>
                                    <?php
                                        $path = $item['path'] ?? '/';
                                        $href = preg_match('~^https?://~', $path) ? $path : $baseHref . '/' . ltrim($path, '/');
                                    ?>
                                    <a href="<?= e($href); ?>" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors">
                                        <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-300/70"></span>
                                        <?= e($item['label'] ?? 'Link'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                </div>
            </section>

            <section class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-6 flex flex-col h-full text-sm">
                <div class="flex-0">
                    <p class="font-semibold text-white tracking-wide text-sm">Jam Layanan</p>
                    <p class="mt-1 text-slate-400">Senin-Jumat · 08.00-16.00 WIB</p>
                </div>
                <div class="flex-0">
                    <p class="font-semibold text-white tracking-wide text-sm mb-2">Ikuti pembaruan</p>
                    <div class="flex items-center gap-3">
                        <?php
                            $socials = [
                                ['label' => 'LinkedIn', 'icon' => '<path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8h4V24h-4zM8.5 8h3.8v2.2h.06c.53-1 1.82-2.2 3.75-2.2 4.01 0 4.75 2.64 4.75 6.07V24h-4v-7.75c0-1.85-.03-4.24-2.6-4.24-2.6 0-3 2.03-3 4.1V24h-4z"/>'],
                                ['label' => 'YouTube', 'icon' => '<path d="M23.5 6.2s-.2-1.6-.8-2.3c-.8-.9-1.6-.9-2-.9-2.8-.2-7-.2-7-.2h-.1s-4.2 0-7 .2c-.4 0-1.2 0-2 .9-.6.7-.8 2.3-.8 2.3S4 8.1 4 10v1.9c0 1.9.2 3.8.2 3.8s.2 1.6.8 2.3c.8.9 1.9.9 2.4 1 1.7.2 7.1.2 7.1.2s4.2 0 7-.2c.4 0 1.2 0 2-.9.6-.7.8-2.3.8-2.3s.2-1.9.2-3.8V10c0-1.9-.2-3.8-.2-3.8zM9.75 14.51V8.99l5.32 2.76-5.32 2.76z"/>'],
                                ['label' => 'Instagram', 'icon' => '<path d="M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm10 2c1.7 0 3 1.3 3 3v10c0 1.7-1.3 3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3h10zm-5 3.5A5.5 5.5 0 1017.5 13 5.5 5.5 0 0012 7.5zm0 2A3.5 3.5 0 1115.5 13 3.5 3.5 0 0112 9.5zm6.25-2.75a1.25 1.25 0 10-1.25 1.25 1.25 1.25 0 001.25-1.25z"/>'],
                            ];
                        ?>
                        <?php foreach ($socials as $social): ?>
                            <a class="w-9 h-9 rounded-full border border-white/15 flex items-center justify-center hover:border-emerald-300 hover:text-emerald-300 transition-colors" href="#" aria-label="<?= e($social['label']); ?>">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><?= $social['icon']; ?></svg>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-auto">
                    <p class="font-semibold text-white tracking-wide text-sm mb-2">Butuh bantuan?</p>
                    <a href="<?= $baseHref; ?>/kontak" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-400/60 text-sm text-white hover:bg-emerald-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 4-6 4-6 0V6a6 6 0 1112 0v5c0 4-6 4-6 0zm0 8v-2" /></svg>
                        Hubungi tim repository
                    </a>
                </div>
            </section>
        </div>
    </div>

    <div class="border-t border-white/10 bg-slate-950/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-xs sm:text-sm text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-center sm:text-left">
                &copy; <?= date('Y'); ?>
                <a href="https://inhafi.ac.id" target="_blank" rel="noopener noreferrer" class="font-medium text-white hover:text-emerald-300 transition-colors">
                    Institut Agama Islam Hasan Jufri
                </a>.
                Seluruh hak cipta dilindungi.
            </p>

            <p class="text-center sm:text-right font-medium text-slate-300">
                Pengembangan sistem oleh
                <a href="#" target="_blank" rel="noopener noreferrer" class="text-white hover:text-emerald-300 transition-colors">
                    VeritasDev
                </a>
                <span class="px-1 text-slate-500">•</span>
                <a href="https://polije.ac.id" target="_blank" rel="noopener noreferrer" class="text-white hover:text-emerald-300 transition-colors">
                    Politeknik Negeri Jember
                </a>
            </p>
        </div>
    </div>
</footer>
