<?php
// --- LOGIKA PHP (TIDAK BERUBAH) ---
$heading = $title ?? 'Hasil Pencarian';
$items = $items ?? [];
$total = isset($total) ? (int) $total : count($items);
?>
<main class="bg-gradient-to-b from-white to-slate-50 min-h-screen">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-10 space-y-5">
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-5 sm:p-7 lg:p-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-[11px] font-semibold tracking-[0.2em] text-emerald-600 uppercase">Hasil Kategori</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 leading-tight"><?= e($heading) ?></h1>
                    <p class="text-sm text-slate-600">Menampilkan dokumen sesuai kategori yang dipilih.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-emerald-100 bg-emerald-50 text-xs font-semibold text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <?= $total; ?> dokumen
                    </span>
                    <a href="<?= $base_url; ?>/telusuri" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-200 text-xs font-semibold text-slate-600 hover:text-emerald-700 hover:border-emerald-300 transition-colors">
                        Kategori lain
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <?php if (empty($items)): ?>
            <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-6 text-center text-sm text-slate-500">
                Tidak ada dokumen pada kategori ini.
            </div>
        <?php else: ?>
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-4 sm:p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <?php foreach ($items as $item): ?>
                        <article class="p-4 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:shadow-md transition-all text-xs sm:text-sm space-y-2">
                            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                <?php if (!empty($item['jenis_karya'])): ?>
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h6" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V5a2 2 0 012-2z" /></svg>
                                        <?= e(ucwords(str_replace('_', ' ', (string) $item['jenis_karya']))) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($item['prodi'] ?? $item['nama_program_studi'] ?? null)): ?>
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium flex items-center gap-1 truncate max-w-full sm:max-w-[80%]">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6l9 4 9-4V7" /></svg>
                                        <span class="truncate"><?= e($item['prodi'] ?? $item['nama_program_studi']) ?></span>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($item['mata_kuliah'])): ?>
                                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-medium flex items-center gap-1 truncate max-w-full sm:max-w-[80%]">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v4H4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 8v12"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16v8H4z"/></svg>
                                        <span class="truncate"><?= e($item['mata_kuliah']) ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <a href="<?= $base_url; ?>/repository/<?= e($item['slug'] ?? '') ?>" class="group/title block">
                                <h2 class="text-sm font-semibold text-slate-900 line-clamp-2 group-hover/title:text-emerald-700 transition-colors">
                                    <?= e($item['judul'] ?? '-') ?>
                                </h2>
                            </a>

                            <p class="text-[11px] text-slate-600 line-clamp-3">
                                <?= e($item['abstrak'] ?? 'Ringkasan belum tersedia.') ?>
                            </p>

                            <div class="border-t border-slate-100 pt-2 mt-1 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                                <span class="flex items-center gap-1 min-w-0 max-w-[72%]">
                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="8" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 20c0-3 2.5-5 6-5s6 2 6 5"></path>
                                    </svg>
                                    <span class="truncate"><?= e($item['author'] ?? '-') ?></span>
                                </span>
                                <span class="flex items-center gap-1 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1z"/></svg>
                                    <?= e($item['tahun'] ?? '-') ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

