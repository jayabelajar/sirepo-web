<?php $base = rtrim($base_url, '/'); ?>

<div class="space-y-4 animate-fade-in-up">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Review Repository</h1>
            <p class="text-sm text-slate-500">Daftar unggahan mahasiswa yang menunggu persetujuan.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <?php if (empty($items)): ?>
            <div class="px-6 py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-slate-800 font-semibold">Semua Beres!</h3>
                <p class="text-slate-500 text-sm mt-1">Tidak ada repository yang menunggu review saat ini.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Repository</th>
                            <th class="px-6 py-4">Penulis / Tahun</th>
                            <th class="px-6 py-4">Prodi</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-slate-900 leading-tight"><?= e($item['judul']) ?></span>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 w-fit px-1.5 py-0.5 rounded"><?= e($item['jenis_karya']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-700"><?= e($item['author']) ?></div>
                                    <div class="text-slate-400 text-xs"><?= e($item['tahun']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-600"><?= e($item['prodi'] ?? '-') ?></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <?php if(!empty($item['file_pdf'])): ?>
                                            <a href="<?= $base ?>/assets/uploads/<?= e($item['file_pdf']) ?>" target="_blank" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Buka PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </a>
                                        <?php endif; ?>

                                        <button type="button" 
                                                onclick="openConfirmModal('<?= $base ?>/admin/repository/<?= $item['id'] ?>/approve', 'Terbitkan Repository', 'Apakah Anda yakin ingin menyetujui dan menerbitkan karya ilmiah ini? Karya akan dapat diakses publik.', 'bg-emerald-600', 'Setujui & Terbitkan', '<?= e($csrf) ?>')"
                                                class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-colors shadow-sm">Setujui</button>

                                        <button type="button"
                                                onclick="openConfirmModal('<?= $base ?>/admin/repository/<?= $item['id'] ?>/reject', 'Tolak Repository', 'Apakah Anda yakin ingin menolak karya ilmiah ini? Mahasiswa perlu melakukan revisi.', 'bg-rose-600', 'Tolak', '<?= e($csrf) ?>', true)"
                                                 class="px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-100 rounded-lg text-xs font-bold hover:bg-rose-100 transition-colors">Tolak</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
