<?php
    $baseHref = rtrim($base_url ?? '', '/');
    $footerLinkGroups = $footerLinkGroups ?? [];
?>
<footer class="mt-12 bg-slate-950 text-slate-300 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <section class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-6 flex flex-col h-full">
                <div class="flex items-start sm:items-center gap-2.5 sm:gap-4">
                    <div class="w-8 h-8 sm:w-12 sm:h-12 lg:w-14 lg:h-14 flex-shrink-0 flex items-center justify-center">
                        <img src="<?= $baseHref; ?>/assets/img/favicon.svg" alt="Logo Sistem Informasi Repository" class="w-full h-full object-contain" />
                    </div>
                    <div class="min-w-0 leading-tight">
                        <p class="text-xs sm:text-sm lg:text-base font-semibold tracking-wide text-white">SISTEM INFORMASI REPOSITORI</p>
                        <p class="mt-0.5 text-[10px] sm:text-xs uppercase tracking-wide font-bold text-emerald-200/70 break-words">Institut Agama Islam Hasan Jufri</p>
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
                    <p class="mt-1 text-slate-400">Senin-Jumat &middot; 08.00-16.00 WIB</p>
                </div>
                <div class="flex-0">
                    <p class="font-semibold text-white tracking-wide text-sm mb-2">Ikuti pembaruan</p>
                    <div class="flex items-center gap-3">
                        <?php
                            $socials = [
                                ['label' => 'Instagram', 'icon' => '<path d="M7 2.5h10A4.5 4.5 0 0121.5 7v10a4.5 4.5 0 01-4.5 4.5H7A4.5 4.5 0 012.5 17V7A4.5 4.5 0 017 2.5zm5 4.5a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6zm5.25-2.13a1.12 1.12 0 10-.01 2.25 1.12 1.12 0 00.01-2.25z"/>'],
                                ['label' => 'Facebook', 'icon' => '<path d="M13.5 22v-7h2.4l.4-3h-2.8V10c0-.86.26-1.45 1.5-1.45H16V5.9c-.45-.06-1.4-.15-2.66-.15-2.63 0-4.34 1.6-4.34 4.55V12H6.5v3H9v7h4.5z"/>'],
                                ['label' => 'YouTube', 'icon' => '<path d="M23 12c0-2.1-.2-3.6-.6-4.5a2.9 2.9 0 00-2-1.7C18.8 5.4 12 5.4 12 5.4s-6.8 0-8.4.4a2.9 2.9 0 00-2 1.7C1.2 8.4 1 9.9 1 12s.2 3.6.6 4.5a2.9 2.9 0 002 1.7c1.6.4 8.4.4 8.4.4s6.8 0 8.4-.4a2.9 2.9 0 002-1.7c.4-.9.6-2.4.6-4.5zm-13.1 3.8V8.2l6.1 3.8-6.1 3.8z"/>'],
                            ];
                        ?>
                        <?php foreach ($socials as $social): ?>
                            <a class="w-10 h-10 rounded-xl border border-white/15 bg-slate-900/60 flex items-center justify-center text-slate-200 hover:border-emerald-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition-all duration-200 hover:-translate-y-0.5" href="#" aria-label="<?= e($social['label']); ?>">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><?= $social['icon']; ?></svg>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-auto">
                    <p class="font-semibold text-white tracking-wide text-sm mb-2">Butuh bantuan?</p>
                    <a href="<?= $baseHref; ?>/kontak" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-400/60 text-sm text-white hover:bg-emerald-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
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
                <a href="https://inhafi.ac.id" target="_blank" rel="noopener noreferrer" class="font-bold text-white hover:text-emerald-300 transition-colors">
                    SIREPO INHAFI
                </a>
                All right reserved.
            </p>

            <p class="text-center sm:text-right font-medium text-slate-300">
               Developed by
                <a href="#" target="_blank" rel="noopener noreferrer" class="text-white hover:text-emerald-300 transition-colors">
                    VeritasDev
                </a>
                <span class="px-1 text-slate-500">&middot;</span>
                <a href="https://polije.ac.id" target="_blank" rel="noopener noreferrer" class="text-white hover:text-emerald-300 transition-colors">
                    Politeknik Negeri Jember
                </a>
            </p>
        </div>
    </div>
</footer>

