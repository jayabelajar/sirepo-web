<?php $base = rtrim($base_url, '/'); ?>

<div class="space-y-4 animate-fade-in-up">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight leading-tight">Repository</h1>
            <p class="text-sm text-slate-500">Daftar repository yang melibatkan Anda.</p>
        </div>
        <div>
            <a href="<?= $base ?>/mahasiswa/my-repository/create" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 shadow-md shadow-emerald-600/10 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Unggah Mandiri</span>
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium animate-pulse">
            <?= e($_GET['success']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium">
            <?= e($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative group lg:col-span-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Cari judul..." 
                       class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
            </div>

            <div>
                <select name="tahun" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer text-slate-600">
                    <option value="">Semua Tahun</option>
                    <?php 
                    $currentYear = date('Y');
                    for($i = $currentYear; $i >= 2010; $i--): ?>
                        <option value="<?= $i ?>" <?= (isset($filters['tahun']) && $filters['tahun'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <select name="jenis" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer text-slate-600">
                    <option value="">Semua Jenis</option>
                    <?php foreach (['skripsi','tugas_akhir','jurnal','artikel','laporan','pkl','lainnya'] as $jk): ?>
                        <option value="<?= $jk ?>" <?= (isset($filters['jenis_karya']) && $filters['jenis_karya'] == $jk) ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ', $jk)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors">Filter</button>
                <a href="<?= $base ?>/mahasiswa/my-repository" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition-colors flex items-center justify-center" title="Reset">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <?php if (empty($repos)): ?>
            <div class="px-6 py-10 text-center text-slate-400">
                <div class="flex flex-col items-center gap-2">
                    <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-sm">Tidak ada data ditemukan.</span>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase tracking-wider text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4 min-w-[250px]">Judul & Jenis</th>
                            <th class="px-6 py-4 whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 min-w-[200px]">Prodi & MK</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($repos as $item): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-1.5">
                                        <?php if(($item['status'] ?? 'approved') === 'approved'): ?>
                                            <a href="<?= $base ?>/repository/<?= $item['slug'] ?>" target="_blank" class="text-slate-900 font-semibold line-clamp-2 leading-snug hover:text-emerald-600 transition-colors">
                                                <?= e($item['judul'] ?? '') ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-900 font-semibold line-clamp-2 leading-snug">
                                                <?= e($item['judul'] ?? '') ?>
                                            </span>
                                        <?php endif; ?>
                                        <div class="flex items-center gap-2">
                                            <?php 
                                                $jenis = strtolower($item['jenis_karya'] ?? 'lainnya');
                                                $badgeColor = match($jenis) {
                                                    'skripsi' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'tugas_akhir' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                    'jurnal' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                    'pkl' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                    default => 'bg-slate-100 text-slate-600 border-slate-200'
                                                };
                                            ?>
                                            <span class="inline-flex w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase border <?= $badgeColor ?>">
                                                <?= e(str_replace('_', ' ', $jenis)) ?>
                                            </span>
                                            <span class="text-slate-400 text-[10px]"><?= e($item['tahun'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top whitespace-nowrap">
                                    <?php 
                                        $status = strtolower($item['status'] ?? 'approved');
                                        $statusColor = match($status) {
                                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'pending'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            default    => 'bg-slate-50 text-slate-700 border-slate-100'
                                        };
                                        $statusLabel = match($status) {
                                            'approved' => 'Terbit',
                                            'pending'  => 'Review',
                                            'rejected' => 'Ditolak',
                                            default    => $status
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusColor ?>">
                                        <?= e($statusLabel) ?>
                                    </span>
                                    <?php if ($status === 'rejected' && !empty($item['rejection_note'])): ?>
                                        <div class="mt-2 p-2.5 bg-rose-50 border border-rose-100 rounded-lg text-[11px] text-rose-700 leading-relaxed max-w-[200px]">
                                            <span class="font-bold block mb-0.5 uppercase tracking-tighter">Alasan Penolakan:</span>
                                            <?= e($item['rejection_note']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="text-slate-600 text-xs space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            <?= e($item['prodi'] ?? '-') ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <?php if($status === 'approved'): ?>
                                            <a href="<?= $base ?>/repository/<?= $item['slug'] ?>" target="_blank" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors border border-transparent hover:border-blue-100" title="Lihat Postingan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-400 italic">Menunggu Review</span>
                                        <?php endif; ?>
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
