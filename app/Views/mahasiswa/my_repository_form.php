<?php
$base   = rtrim($base_url, '/');
$isEdit = isset($repo);
$item   = $repo ?? []; // alias for consistency with admin form
$action = $isEdit
    ? "{$base}/mahasiswa/my-repository/" . ($item['id'] ?? '') . "/update"
    : "{$base}/mahasiswa/my-repository/store";

$prodis  = $prodis ?? [];
$mk_list = $mk_list ?? [];
$mahasiswas = $mahasiswas ?? [];
$dosens     = $dosens ?? [];

$selected_authors   = $relations['authors'] ?? [];
$selected_advisors  = $relations['advisors'] ?? [];
$selected_examiners = $relations['examiners'] ?? [];

// In create mode, uploader is auto-tagged as author if not already
if (!$isEdit && !empty($mhs['id'])) {
    $selected_authors = [$mhs['id']];
}
?>

<div class="space-y-6 animate-fade-in-up">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight leading-tight">
                <?= e($isEdit ? 'Edit Repository' : 'Unggah Mandiri') ?>
            </h1>
            <p class="text-sm text-slate-500 mt-1"><?= $isEdit ? 'Perbarui data repository Anda' : 'Unggah karya ilmiah Anda ke repositori' ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= $base ?>/mahasiswa/my-repository" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <form id="repoForm" action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
        <input type="hidden" name="csrf_token" value="<?= e(\App\Core\Security\Csrf::token()) ?>">
        
        <!-- JUDUL -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Judul Karya Ilmiah <span class="text-rose-500">*</span></label>
            <input type="text" name="judul" value="<?= e($item['judul'] ?? '') ?>" required
                   placeholder="Masukkan judul lengkap karya ilmiah Anda"
                   class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
        </div>

        <!-- JENIS / PENULIS UTAMA / TAHUN -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Jenis Karya <span class="text-rose-500">*</span></label>
                <select name="jenis_karya" required class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
                    <option value="">Pilih jenis karya</option>
                    <?php foreach (['skripsi','tugas_akhir','jurnal','artikel','laporan','pkl','lainnya'] as $jk): ?>
                        <option value="<?= $jk ?>" <?= (($item['jenis_karya'] ?? '') === $jk) ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_',' ', $jk)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Penulis Utama</label>
                <input type="hidden" name="author" id="primaryAuthorName" value="<?= e($item['author'] ?? ($mhs['nama_lengkap'] ?? '')) ?>" data-fallback="<?= e($mhs['nama_lengkap'] ?? '') ?>">
                <input type="text" id="primaryAuthorPreview" value="<?= e($item['author'] ?? ($mhs['nama_lengkap'] ?? '')) ?>" readonly
                       placeholder="Pilih dari Tandai Penulis"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-700 cursor-not-allowed">
                <p class="text-[10px] text-slate-400">Otomatis diambil dari penulis pertama yang ditandai.</p>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Tahun <span class="text-rose-500">*</span></label>
                <input type="number" name="tahun" value="<?= e($item['tahun'] ?? date('Y')) ?>" min="2000" max="<?= date('Y')+1 ?>" required
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
            </div>
        </div>

        <!-- PRODI + MK -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Program Studi <span class="text-rose-500">*</span></label>
                <select name="program_studi_id" required class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
                    <option value="">Pilih program studi</option>
                    <?php foreach ($prodis as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= (!empty($item['program_studi_id']) && (int)$item['program_studi_id'] === (int)$p['id']) ? 'selected' : '' ?>>
                            <?= e($p['nama_program_studi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Mata Kuliah (Opsional)</label>
                <select name="mata_kuliah_id" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
                    <option value="">Pilih mata kuliah</option>
                    <?php foreach ($mk_list as $mk): ?>
                        <option value="<?= $mk['id'] ?>" <?= (!empty($item['mata_kuliah_id']) && (int)$item['mata_kuliah_id'] === (int)$mk['id']) ? 'selected' : '' ?>>
                            <?= e($mk['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Kata Kunci</label>
            <input type="text" name="keywords" value="<?= e($item['keywords'] ?? '') ?>" placeholder="Pisahkan dengan koma, contoh: web, sistem pakar, php"
                   class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Abstrak</label>
            <textarea name="abstrak" rows="5" placeholder="Tuliskan abstrak karya ilmiah Anda di sini..."
                      class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700"><?= e($item['abstrak'] ?? '') ?></textarea>
        </div>

        <!-- TANDAI ORANG -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tandai Orang Terkait</h3>
            
            <div class="space-y-4">
                <!-- Tandai PENULIS -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <span>Penulis (Mahasiswa/Lainnya)</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <button type="button" data-dd-btn="author" onclick="ddToggle('author')"
                                class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-left flex justify-between items-center hover:border-emerald-500 transition-colors">
                            <span id="authorLabel" class="text-slate-500 truncate pr-4">Pilih penulis...</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="dd-author" class="hidden absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden ring-1 ring-slate-900/5">
                            <div class="p-2 border-b border-slate-100 bg-slate-50">
                                <input type="text" id="authorSearch" placeholder="Cari nama / NIM..."
                                       oninput="ddFilter('author', this.value)"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            </div>

                            <div class="max-h-60 overflow-auto custom-scrollbar">
                                <?php
                                $authorSources = [
                                    ['items' => $mahasiswas, 'role' => 'mahasiswa'],
                                    ['items' => $dosens, 'role' => 'dosen'],
                                ];
                                ?>
                                <?php foreach ($authorSources as $src): ?>
                                    <?php foreach ($src['items'] as $m): ?>
                                        <?php
                                          $id  = (int)($m['id'] ?? 0);
                                          $nm  = ($m['nama_lengkap'] ?? $m['username'] ?? 'User');
                                          $code = ($src['role'] === 'dosen' ? ($m['nidn_nip'] ?? '') : ($m['nim'] ?? ''));
                                          $txt = trim($nm . ($code ? " ($code)" : ""));
                                          $checked = in_array($id, $selected_authors, true);
                                        ?>
                                        <label class="dd-author-item flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50 cursor-pointer transition-colors group"
                                               data-text="<?= e(mb_strtolower($txt)) ?>">
                                            <input type="checkbox" class="dd-author-checkbox w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" name="authors[]" value="<?= $id ?>"
                                                   data-role="<?= $src['role'] ?>"
                                                   data-name="<?= e($nm) ?>"
                                                   <?= $checked ? 'checked' : '' ?>
                                                   onchange="ddSync('author')">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-700 group-hover:text-emerald-700"><?= e($nm) ?></p>
                                                <p class="text-[10px] text-slate-400"><?= e($code ?: ucfirst($src['role'])) ?></p>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tandai PEMBIMBING -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Dosen Pembimbing</label>
                        <div class="relative">
                            <button type="button" data-dd-btn="advisor" onclick="ddToggle('advisor')"
                                    class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-left flex justify-between items-center hover:border-emerald-500 transition-colors">
                                <span id="advisorLabel" class="text-slate-500 truncate pr-4">Pilih pembimbing...</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div id="dd-advisor" class="hidden absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden ring-1 ring-slate-900/5">
                                <div class="p-2 border-b border-slate-100 bg-slate-50">
                                    <input type="text" id="advisorSearch" placeholder="Cari nama / NIDN..."
                                           oninput="ddFilter('advisor', this.value)"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div class="max-h-60 overflow-auto custom-scrollbar">
                                    <?php foreach ($dosens as $d): ?>
                                        <?php
                                          $id  = (int)($d['id'] ?? 0);
                                          $nm  = ($d['nama_lengkap'] ?? $d['username'] ?? 'Dosen');
                                          $nid = ($d['nidn_nip'] ?? '');
                                          $txt = trim($nm . ($nid ? " ($nid)" : ""));
                                          $checked = in_array($id, $selected_advisors, true);
                                        ?>
                                        <label class="dd-advisor-item flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50 cursor-pointer transition-colors group"
                                               data-text="<?= e(mb_strtolower($txt)) ?>">
                                            <input type="checkbox" class="dd-advisor-checkbox w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" name="advisors[]" value="<?= $id ?>"
                                                   <?= $checked ? 'checked' : '' ?>
                                                   onchange="ddSync('advisor')">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-700 group-hover:text-emerald-700"><?= e($nm) ?></p>
                                                <p class="text-[10px] text-slate-400"><?= e($nid ?: 'Dosen') ?></p>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tandai PENGUJI -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Dosen Penguji</label>
                        <div class="relative">
                            <button type="button" data-dd-btn="examiner" onclick="ddToggle('examiner')"
                                    class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-left flex justify-between items-center hover:border-emerald-500 transition-colors">
                                <span id="examinerLabel" class="text-slate-500 truncate pr-4">Pilih penguji...</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div id="dd-examiner" class="hidden absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden ring-1 ring-slate-900/5">
                                <div class="p-2 border-b border-slate-100 bg-slate-50">
                                    <input type="text" id="examinerSearch" placeholder="Cari nama / NIDN..."
                                           oninput="ddFilter('examiner', this.value)"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div class="max-h-60 overflow-auto custom-scrollbar">
                                    <?php foreach ($dosens as $d): ?>
                                        <?php
                                          $id  = (int)($d['id'] ?? 0);
                                          $nm  = ($d['nama_lengkap'] ?? $d['username'] ?? 'Dosen');
                                          $nid = ($d['nidn_nip'] ?? '');
                                          $txt = trim($nm . ($nid ? " ($nid)" : ""));
                                          $checked = in_array($id, $selected_examiners, true);
                                        ?>
                                        <label class="dd-examiner-item flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50 cursor-pointer transition-colors group"
                                               data-text="<?= e(mb_strtolower($txt)) ?>">
                                            <input type="checkbox" class="dd-examiner-checkbox w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" name="examiners[]" value="<?= $id ?>"
                                                   <?= $checked ? 'checked' : '' ?>
                                                   onchange="ddSync('examiner')">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-700 group-hover:text-emerald-700"><?= e($nm) ?></p>
                                                <p class="text-[10px] text-slate-400"><?= e($nid ?: 'Dosen') ?></p>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILE PDF -->
        <div class="space-y-3 pt-4 border-t border-slate-100">
            <label class="text-sm font-semibold text-slate-700">File PDF <span class="text-rose-500">*</span></label>
            <?php if ($isEdit && !empty($item['file_pdf'])): ?>
                <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-100 rounded-lg">
                    <div class="text-sm text-emerald-800">
                        File terunggah: <span class="font-semibold"><?= e($item['file_pdf']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <input type="file" name="file_pdf" accept="application/pdf" <?= $isEdit ? '' : 'required' ?>
                   class="w-full text-sm text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="text-xs text-slate-400 italic">Hanya file PDF, maksimal 10MB.</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="<?= $base ?>/mahasiswa/my-repository" class="px-4 py-2.5 rounded-lg text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span><?= $isEdit ? 'Simpan Perubahan' : 'Mulai Unggah' ?></span>
            </button>
        </div>
    </form>

    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-sm text-blue-700 leading-relaxed">
            <strong>Informasi:</strong> Setelah mengunggah, karya ilmiah Anda akan berstatus <span class="font-bold">Pending</span> dan memerlukan review oleh Admin sebelum diterbitkan secara publik.
        </div>
    </div>
</div>

<script>
function ddToggle(key) {
  const dd = document.getElementById('dd-' + key);
  if (!dd) return;
  dd.classList.toggle('hidden');
  if (!dd.classList.contains('hidden')) {
    const search = document.getElementById(key + 'Search');
    if (search) setTimeout(() => search.focus(), 0);
  }
}

function ddFilter(key, query) {
  const q = (query || '').toLowerCase().trim();
  document.querySelectorAll('.dd-' + key + '-item').forEach(item => {
    const text = item.getAttribute('data-text') || '';
    item.style.display = text.includes(q) ? '' : 'none';
  });
}

function ddSync(key) {
    const label = document.getElementById(key + 'Label');
    if (!label) return;
    const checked = document.querySelectorAll('.dd-' + key + '-checkbox:checked');
    const picked = [];
    checked.forEach(cb => {
        const text = cb.closest('label').querySelector('p.text-sm')?.textContent.trim() || '';
        if (text) picked.push(text);
    });
  const placeholders = { author: 'Pilih penulis...', advisor: 'Pilih pembimbing...', examiner: 'Pilih penguji...' };
  label.textContent = picked.length ? picked.join(', ') : (placeholders[key] || 'Pilih...');
  if (key === 'author') updatePrimaryAuthor();
}

function updatePrimaryAuthor() {
  const hidden = document.getElementById('primaryAuthorName');
  const preview = document.getElementById('primaryAuthorPreview');
  if (!hidden || !preview) return;
  const checked = document.querySelectorAll('.dd-author-checkbox:checked');
  const fallback = (hidden.getAttribute('data-fallback') || '').trim();
  let primaryName = '';
  checked.forEach(cb => {
    if (primaryName) return;
    const candidate = (cb.getAttribute('data-name') || '').trim();
    if (candidate) primaryName = candidate;
  });
  if (!primaryName) primaryName = fallback;
  hidden.value = primaryName;
  preview.value = primaryName;
}

document.addEventListener('DOMContentLoaded', () => {
  ddSync('author');
  ddSync('advisor');
  ddSync('examiner');
});

document.addEventListener('click', (e) => {
  ['author','advisor','examiner'].forEach(key => {
    const btn = document.querySelector('[data-dd-btn="' + key + '"]');
    const dd  = document.getElementById('dd-' + key);
    if (!btn || !dd) return;
    if (!btn.contains(e.target) && !dd.contains(e.target)) dd.classList.add('hidden');
  });
});

document.getElementById('repoForm')?.addEventListener('submit', function(e){
  const pickedAuthors = document.querySelectorAll('.dd-author-checkbox:checked').length;
  if (pickedAuthors < 1) {
    e.preventDefault();
    alert('Minimal pilih 1 penulis (Tandai Penulis).');
    ddToggle('author');
  }
});
</script>
