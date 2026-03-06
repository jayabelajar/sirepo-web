<?php
  $authors = $authors ?? [];
  $totalDocs = 0;
  foreach ($authors as $a) {
      $totalDocs += (int) ($a['total'] ?? 0);
  }
?>
<main class="bg-gradient-to-b from-white to-slate-50 min-h-screen">
  <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-10 space-y-5">
    <div class="relative overflow-hidden bg-white border border-slate-200 rounded-3xl shadow-sm p-5 sm:p-7 lg:p-8">
      <div class="absolute -top-14 -right-10 w-40 h-40 rounded-full bg-emerald-100/50 blur-2xl"></div>
      <div class="relative flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div class="space-y-1">
          <p class="text-[11px] font-semibold tracking-[0.2em] text-emerald-600 uppercase">Telusuri</p>
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 leading-tight">Penelusuran Berdasarkan Penulis</h1>
          <p class="text-sm text-slate-600">Pilih penulis untuk menampilkan dokumen yang dipublikasikan.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-emerald-100 bg-emerald-50 text-xs font-semibold text-emerald-700">
          <span><?= count($authors); ?> penulis</span>
          <span class="text-emerald-300">•</span>
          <span><?= $totalDocs; ?> dokumen</span>
        </div>
      </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-4 sm:p-5">
      <?php if (empty($authors)): ?>
        <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
          Data penulis belum tersedia.
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
          <?php foreach ($authors as $a): ?>
            <?php $authorName = (string) ($a['label'] ?? ''); ?>
            <a href="<?= $base_url; ?>/telusuri/author/<?= rawurlencode($authorName); ?>"
               class="group relative overflow-hidden p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-300 hover:shadow-md transition">
              <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-emerald-400/0 via-emerald-400/80 to-emerald-400/0 opacity-0 group-hover:opacity-100 transition-opacity"></div>
              <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="7" r="4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 21c0-4 3-7 7-7s7 3 7 7"/>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="font-semibold text-slate-900 line-clamp-2 group-hover:text-emerald-700 transition-colors"><?= e($authorName !== '' ? $authorName : '-'); ?></p>
                  <p class="text-xs text-slate-500"><?= (int) ($a['total'] ?? 0); ?> dokumen</p>
                </div>
                <div class="text-slate-400 group-hover:text-emerald-600 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

