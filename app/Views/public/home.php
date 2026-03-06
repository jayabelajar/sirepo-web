<?php $base = rtrim($base_url ?? '', '/'); ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12 pb-12 sm:pb-16">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        
        <div class="space-y-4">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-[2.6rem] font-bold text-slate-900 leading-snug sm:leading-tight max-w-3xl">
                Sistem Informasi Repositori <br class="hidden sm:block">Institut Agama Islam Hasan Jufri
            </h1>

            <p class="text-slate-600 text-sm sm:text-base lg:text-lg max-w-2xl">
                Akses publikasi ilmiah kampus secara terstruktur untuk kebutuhan pembelajaran, riset, dan rujukan akademik.
            </p>

            <div class="pt-4 flex flex-wrap gap-3">
                <a href="<?= $base ?>/telusuri/"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Telusuri Repositori
                </a>

                <a href="<?= $base ?>/download"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg border border-slate-300 text-sm text-slate-700 font-semibold hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Aplikasi
                </a>
            </div>
        </div>

        <div class="relative hidden md:flex items-center justify-center">
            <div class="w-full h-80 bg-emerald-50 rounded-3xl absolute opacity-50"></div>
            
            <div class="relative bg-white border border-slate-200 rounded-2xl p-6 w-11/12 shadow-lg">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.247m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.247"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Publikasi Terbaru</p>
                        <p class="text-xs text-slate-500">Ringkasan dokumen yang baru dipublikasikan</p>
                    </div>
                </div>

                <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                    <?php foreach (array_slice($latest, 0, 5) as $repo): ?>
                        <a href="<?= $base ?>/repository/<?= e($repo['slug']) ?>"
                           class="block p-3 rounded-lg hover:bg-slate-50 transition-colors border-l-4 border-transparent hover:border-emerald-500">
                            
                            <p class="text-xs font-medium text-slate-800 line-clamp-1 hover:text-emerald-700">
                                <?= e($repo['judul']) ?>
                            </p>

                            <div class="flex items-center gap-3 mt-0.5 text-[11px] text-slate-500">
                                <span>Tahun: <?= e($repo['tahun']) ?></span>
                                <span class="truncate">Oleh: <?= e($repo['author']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ✅ DIUBAH: pb-16/sm:pb-20 -> pb-8/sm:pb-10 + tambah deskripsi -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 sm:pb-10">
    <style>
        @media (max-width: 639px) {
            .category-slider {
                display: flex;
                gap: 0.875rem;
                overflow-x: auto;
                padding: 0 0.25rem 0.5rem;
                scroll-snap-type: x mandatory;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .category-slider::-webkit-scrollbar {
                display: none;
            }
            .category-slider-card {
                flex: 0 0 10.75rem;
                min-width: 10.75rem;
                aspect-ratio: 1 / 1;
                scroll-snap-align: start;
            }
        }
    </style>
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl sm:text-2xl font-semibold text-slate-900 border-l-4 border-emerald-500 pl-3">Kategori Repositori</h2>

        <div class="sm:hidden flex items-center gap-2 pr-1">
            <button id="categorySliderPrev" type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-emerald-700 hover:border-emerald-400 transition-colors" aria-label="Slide kategori sebelumnya">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>
            <button id="categorySliderNext" type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-emerald-700 hover:border-emerald-400 transition-colors" aria-label="Slide kategori berikutnya">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5L15.75 12l-7.5 7.5" />
                </svg>
            </button>
        </div>
    </div>
    <p class="text-sm text-slate-500 mt-1 pl-3 mb-6">
        Pilih kategori untuk mempersempit penelusuran dokumen ilmiah.
    </p>

    <div id="categorySlider" class="category-slider sm:grid sm:grid-cols-3 lg:grid-cols-5 sm:gap-4 lg:gap-5 text-sm">

        <a href="<?= $base ?>/telusuri/year"
           class="category-slider-card flex flex-col items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 p-4 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1z" />
                </svg>
            </div>
            <div class="text-center">
                <p class="font-semibold text-slate-800 mt-1">Per Tahun</p>
                <p class="text-xs text-slate-500 line-clamp-2">Berdasarkan tahun terbit</p>
            </div>
        </a>

        <a href="<?= $base ?>/telusuri/program-studi"
           class="category-slider-card flex flex-col items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 p-4 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6l9 4 9-4V7" />
                </svg>
            </div>
            <div class="text-center">
                <p class="font-semibold text-slate-800 mt-1">Program Studi</p>
                <p class="text-xs text-slate-500 line-clamp-2">Berdasarkan program studi</p>
            </div>
        </a>

        <a href="<?= $base ?>/telusuri/mata-kuliah"
           class="category-slider-card flex flex-col items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 p-4 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v4H4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M10 8v12" /><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16v8H4z" />
                </svg>
            </div>
            <div class="text-center">
                <p class="font-semibold text-slate-800 mt-1">Mata Kuliah</p>
                <p class="text-xs text-slate-500 line-clamp-2">Berdasarkan mata kuliah</p>
            </div>
        </a>

        <a href="<?= $base ?>/telusuri/jenis-karya"
           class="category-slider-card flex flex-col items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 p-4 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v4H5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14v8H5z" />
                </svg>
            </div>
            <div class="text-center">
                <p class="font-semibold text-slate-800 mt-1">Jenis Publikasi</p>
                <p class="text-xs text-slate-500 line-clamp-2">Jenis dokumen ilmiah</p>
            </div>
        </a>

        <a href="<?= $base ?>/telusuri/author"
           class="category-slider-card flex flex-col items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 p-4 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 20c0-3 2.5-5 6-5s6 2 6 5"></path>
                </svg>
            </div>
            <div class="text-center">
                <p class="font-semibold text-slate-800 mt-1">Penulis</p>
                <p class="text-xs text-slate-500 line-clamp-2">Berdasarkan penulis</p>
            </div>
        </a>

    </div>
</section>

<script>
    (() => {
        const slider = document.getElementById('categorySlider');
        const prevBtn = document.getElementById('categorySliderPrev');
        const nextBtn = document.getElementById('categorySliderNext');

        if (!slider || !prevBtn || !nextBtn) {
            return;
        }

        const getStep = () => {
            const card = slider.querySelector('.category-slider-card');
            if (!card) {
                return slider.clientWidth * 0.8;
            }
            const sliderStyle = window.getComputedStyle(slider);
            const gap = parseFloat(sliderStyle.gap || '0');
            return card.getBoundingClientRect().width + gap;
        };

        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -getStep(), behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: getStep(), behavior: 'smooth' });
        });
    })();
</script>


<!-- ✅ DIUBAH: pt-12 -> pt-4 biar jarak lebih dekat -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20 pt-4">
    <style>
        @media (max-width: 639px) {
            .latest-repo-link-desktop {
                display: none;
            }
        }
        @media (min-width: 640px) {
            .latest-repo-link-mobile {
                display: none;
            }
        }
    </style>
    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-semibold text-slate-900 border-l-4 border-emerald-500 pl-3">Publikasi Terbaru</h2>
        <div class="mt-1 pl-3 flex items-start sm:items-center justify-between gap-3">
            <p class="text-sm text-slate-500">Dokumen ilmiah yang baru diarsipkan pada repositori.</p>
            <a href="<?= $base ?>/telusuri/"
               class="latest-repo-link-desktop group inline-flex items-center gap-1 text-sm font-medium text-emerald-700 hover:text-emerald-800 hover:underline flex-shrink-0 whitespace-nowrap">
                Lihat Semua Repositori
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>

    <?php $latestGridItems = array_slice($latest ?? [], 0, 4); ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-5 lg:gap-6">
        <?php foreach ($latestGridItems as $item): ?>
            <article class="group flex flex-col h-full p-3 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 hover:border-emerald-300 transition-all duration-200">

                <div class="flex flex-wrap items-center gap-2 text-xs mb-2">
                    <?php if (!empty($item['jenis_karya'])): ?>
                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h6" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V5a2 2 0 012-2z" /></svg>
                            <?= e(ucwords(str_replace('_', ' ', (string) $item['jenis_karya']))) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($item['prodi'])): ?>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium flex items-center gap-1 truncate max-w-full sm:max-w-[80%]">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6l9 4 9-4V7" /></svg>
                            <span class="truncate"><?= e($item['prodi']) ?></span>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($item['mata_kuliah'])): ?>
                        <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-medium flex items-center gap-1 truncate max-w-full sm:max-w-[80%]">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v4H4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 8v12"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16v8H4z"/></svg>
                            <span class="truncate"><?= e($item['mata_kuliah']) ?></span>
                        </span>
                    <?php endif; ?>
                </div>

                <a href="<?= $base ?>/repository/<?= e($item['slug'] ?? '') ?>" class="block focus:outline-none">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-900 line-clamp-2 hover:text-emerald-700 transition-colors">
                        <?= e($item['judul']) ?>
                    </h3>
                </a>

                <p class="mt-1 text-[11px] text-slate-600 line-clamp-2">
                    <?= e($item['abstrak'] ?? 'Abstrak belum tersedia.') ?>
                </p>

                <div class="mt-3 border-t border-slate-100 pt-3 flex items-center justify-between gap-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1 min-w-0 max-w-[72%]">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 20c0-3 2.5-5 6-5s6 2 6 5"></path>
                        </svg>
                        <span class="truncate"><?= e($item['author']) ?></span>
                    </span>
                    <span class="flex items-center gap-1 flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1z"/></svg>
                        <?= e($item['tahun'] ?? '-') ?>
                    </span>
                </div>

            </article>
        <?php endforeach; ?>
    </div>

    <div class="latest-repo-link-mobile mt-5 flex justify-end">
        <a href="<?= $base ?>/telusuri/"
           class="group inline-flex items-center gap-1 text-sm font-medium text-emerald-700 hover:text-emerald-800 hover:underline">
            Lihat Semua Repositori
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6 sm:pb-8"> 
    <div class="rounded-xl bg-slate-900 text-white px-8 py-10 sm:px-10 sm:py-12
                 flex flex-col lg:flex-row items-center justify-between gap-6">

        <div class="lg:max-w-lg">
            <h3 class="text-2xl sm:text-3xl font-semibold leading-snug">
                Akses Repositori Akademik Secara Fleksibel
            </h3>
            <p class="mt-2 text-sm text-slate-300">
                Gunakan repositori sebagai rujukan resmi karya ilmiah institusi.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto flex-shrink-0">
            <a href="<?= $base ?>/telusuri/"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-emerald-500 text-slate-900 text-sm font-semibold hover:bg-emerald-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Telusuri Repositori
            </a>

            <a href="<?= $base ?>/download"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-slate-600 text-sm font-medium text-slate-300 hover:bg-slate-800 transition-colors">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh Aplikasi
                </a>
        </div>

    </div>
</section>
