<?php $base = rtrim($base_url, '/'); ?>
<?php
$panelPrefix = rtrim($panel_prefix ?? '/admin', '/');
if ($panelPrefix === '') {
    $panelPrefix = '/admin';
}
$actorRole = $actor_role ?? 'admin';
$isAdminActor = $actorRole === 'admin';
?>
<div class="space-y-4 animate-fade-in-up">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight leading-tight">Pengguna</h1>
            <p class="text-sm text-slate-500">Kelola admin, developer, dosen, dan mahasiswa.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <form action="<?= $base . $panelPrefix ?>/users/export" method="GET">
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v6m8-6v6m-9 4h10"/></svg>
                    Export CSV
                </button>
            </form>
            <button id="btnImportCsv" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Import CSV
            </button>
            <button id="btnAddUser" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                Tambah
            </button>
            <form id="formImportCsv" action="<?= $base . $panelPrefix ?>/users/import" method="POST" enctype="multipart/form-data" class="hidden">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="file" name="csv_file" id="inputCsv" accept=".csv">
            </form>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2 relative group">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="search" name="q" value="<?= e($search ?? '') ?>" placeholder="Cari nama/email/username" 
                       class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
            </div>
            <div>
                <select name="role" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer text-slate-600">
                    <option value="">Semua Role</option>
                    <?php foreach (['admin','developer','dosen','mahasiswa'] as $r): ?>
                        <option value="<?= $r ?>" <?= ($filter_role === $r) ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Terapkan</button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase tracking-wider text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Identifier</th>
                        <th class="px-6 py-4 whitespace-nowrap">Dibuat</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <?php 
                                $isBanned = !empty($item['banned_until']) && strtotime($item['banned_until']) > time();
                                $developerProtectedByAdmin = (($item['role'] ?? '') === 'developer') && $isAdminActor;
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 align-top whitespace-nowrap">
                                    <div class="text-slate-900 font-semibold"><?= e($item['nama_lengkap'] ?? '') ?></div>
                                    <div class="text-xs text-slate-400">ID: <?= (int) $item['id'] ?></div>
                                </td>
                                <td class="px-6 py-4 align-top text-slate-700"><?= e($item['email'] ?? '') ?></td>
                                <td class="px-6 py-4 align-top">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 capitalize">
                                        <?= e($item['role'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-slate-700">
                                    <?= e($item['nim'] ?? $item['nidn_nip'] ?? $item['username'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 align-top text-slate-600"><?= e($item['created_at'] ?? '') ?></td>
                                <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <?php if ($developerProtectedByAdmin): ?>
                                            <button class="p-2 rounded-lg text-slate-300 border border-transparent cursor-not-allowed" title="Akun developer tidak bisa diedit oleh admin">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        <?php else: ?>
                                            <button
                                                class="p-2 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors border border-transparent hover:border-emerald-100 btnEditUser"
                                                data-id="<?= $item['id'] ?>"
                                                data-nama="<?= e($item['nama_lengkap'] ?? '') ?>"
                                                data-email="<?= e($item['email'] ?? '') ?>"
                                                data-username="<?= e($item['username'] ?? '') ?>"
                                                data-role="<?= e($item['role'] ?? '') ?>"
                                                data-nim="<?= e($item['nim'] ?? '') ?>"
                                                data-nidn="<?= e($item['nidn_nip'] ?? '') ?>"
                                                title="Edit"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($developerProtectedByAdmin): ?>
                                            <button class="p-2 rounded-lg text-slate-300 border border-transparent cursor-not-allowed" title="Akun developer tidak bisa dibekukan oleh admin">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-10v4m8 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                                            </button>
                                        <?php else: ?>
                                            <form action="<?= $base . $panelPrefix ?>/users/<?= $item['id'] ?>/toggle-ban" method="POST" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                                                <button class="p-2 rounded-lg <?= $isBanned ? 'text-orange-600 hover:bg-orange-50 hover:border-orange-100' : 'text-slate-400 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100' ?> border border-transparent transition-colors" title="<?= $isBanned ? 'Buka blokir' : 'Blokir pengguna' ?>">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-10v4m8 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($developerProtectedByAdmin): ?>
                                            <button class="p-2 rounded-lg text-slate-300 border border-transparent cursor-not-allowed" title="Akun developer tidak bisa dihapus oleh admin">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </button>
                                        <?php else: ?>
                                            <form action="<?= $base . $panelPrefix ?>/users/<?= $item['id'] ?>/delete" method="POST" onsubmit="return confirm('Hapus pengguna ini?')" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                                                <button class="p-2 rounded-lg text-red-500 hover:bg-red-50 hover:border-red-100 border border-transparent transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-sm">Belum ada data.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (($pages ?? 1) > 1): ?>
            <?php
                $currentPage = $page ?? 1;
                $totalPages  = $pages ?? 1;
                $queryParams = $_GET;
                unset($queryParams['page']);
            ?>
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-600">
                <span>Halaman <?= $currentPage ?> dari <?= $totalPages ?></span>
                <div class="flex gap-1">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php $queryParams['page'] = $p; $qs = http_build_query($queryParams); ?>
                        <a href="?<?= $qs ?>"
                           class="px-3 py-1 rounded-lg border <?= $p === $currentPage ? 'bg-emerald-600 text-white border-emerald-600' : 'border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Drawer -->
<div id='drawerUser' class='fixed inset-0 z-50 hidden'>
    <div id='drawerUserOverlay' class='absolute inset-0 bg-black/40 backdrop-blur-sm'></div>
    <aside id='drawerUserPanel' class='absolute inset-y-0 right-0 w-full max-w-4xl bg-white border-l border-slate-200 shadow-2xl overflow-y-auto' style='transform: translateX(100%); transition: transform 0.2s ease-out;'>
        <div class='p-6 md:p-8'>
            <div class='flex items-start justify-between gap-3 mb-5'>
                <div class='flex items-start gap-3'>
                    <div class='w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center'>
                        <svg class='w-5 h-5' viewBox='0 0 24 24' fill='none' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 0c-4 0-7 2-7 6h14c0-4-3-6-7-6Z'/></svg>
                    </div>
                    <div>
                        <h3 id='modalTitleUser' class='text-lg font-semibold text-slate-900'>Tambah Pengguna</h3>
                        <p class='text-sm text-slate-500'>Lengkapi informasi pengguna. Username opsional, password isi untuk set/ganti.</p>
                    </div>
                </div>
                <button id='closeModalUser' class='text-slate-400 hover:text-slate-600 transition'>
                    <span class='sr-only'>Tutup</span>
                    <svg class='w-5 h-5' viewBox='0 0 24 24' fill='none' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 6l12 12M6 18L18 6'/></svg>
                </button>
            </div>

            <form id='formUser' method='POST' action='<?= $base . $panelPrefix ?>/users' class='grid grid-cols-1 md:grid-cols-2 gap-4 text-sm'>
                <input type='hidden' name='csrf_token' value='<?= e($csrf) ?>'>
                <div>
                    <label class='text-slate-700 font-medium'>Nama Lengkap</label>
                    <input type='text' name='nama_lengkap' required class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none'>
                </div>
                <div>
                    <label class='text-slate-700 font-medium'>Email</label>
                    <input type='email' name='email' required class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none'>
                </div>
                <div>
                    <label class='text-slate-700 font-medium'>Username</label>
                    <input type='text' name='username' class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none'>
                </div>
                <div>
                    <label class='text-slate-700 font-medium'>Role</label>
                    <select name='role' class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none'>
                        <option value='admin'>Admin</option>
                        <?php if (!$isAdminActor): ?>
                            <option value='developer'>Developer</option>
                        <?php endif; ?>
                        <option value='dosen'>Dosen</option>
                        <option value='mahasiswa'>Mahasiswa</option>
                    </select>
                </div>
                <div>
                    <label class='text-slate-700 font-medium'>NIM (Mahasiswa)</label>
                    <input type='text' name='nim' class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none'>
                </div>
                <div>
                    <label class='text-slate-700 font-medium'>NIDN/NIP (Dosen)</label>
                    <input type='text' name='nidn_nip' class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none'>
                </div>
                <div>
                    <label class='text-slate-700 font-medium'>Password</label>
                    <input type='password' name='password' class='w-full mt-1 px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none' placeholder='Isi untuk set / ganti'>
                </div>
                <div class='md:col-span-2 flex flex-col sm:flex-row justify-end gap-2 mt-2'>
                    <button type='button' id='btnCancelUser' class='px-4 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50'>Batal</button>
                    <button type='submit' id='btnSaveAndNewUser' name='save_and_new' value='1' class='px-4 py-2.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 text-sm font-semibold hover:bg-emerald-100 transition-colors'>Simpan &amp; Tambah Lagi</button>
                    <button class='px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm'>Simpan</button>
                </div>
            </form>
        </div>
    </aside>
</div>

<script>
(() => {
    const drawer = document.getElementById('drawerUser');
    const panel = document.getElementById('drawerUserPanel');
    const overlay = document.getElementById('drawerUserOverlay');
    const btnAdd = document.getElementById('btnAddUser');
    const btnClose = document.getElementById('closeModalUser');
    const btnCancel = document.getElementById('btnCancelUser');
    const btnSaveAndNew = document.getElementById('btnSaveAndNewUser');
    const form = document.getElementById('formUser');
    const title = document.getElementById('modalTitleUser');

    const openModal = () => {
        if (!drawer || !panel) return;
        drawer.classList.remove('hidden');
        requestAnimationFrame(() => {
            panel.style.transform = 'translateX(0)';
        });
    };
    const closeModal = () => {
        if (!drawer || !panel) return;
        panel.style.transform = 'translateX(100%)';
        setTimeout(() => {
            drawer.classList.add('hidden');
            resetForm();
        }, 200);
    };
    const resetForm = () => {
        form.reset();
        form.action = '<?= $base . $panelPrefix ?>/users';
        title.textContent = 'Tambah Pengguna';
        btnSaveAndNew?.classList.remove('hidden');
    };

    btnAdd?.addEventListener('click', () => { resetForm(); openModal(); });
    btnClose?.addEventListener('click', closeModal);
    btnCancel?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer && !drawer.classList.contains('hidden')) {
            closeModal();
        }
    });

    document.querySelectorAll('.btnEditUser').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            form.action = '<?= $base . $panelPrefix ?>/users/' + id + '/update';
            form.nama_lengkap.value = btn.dataset.nama || '';
            form.email.value = btn.dataset.email || '';
            form.username.value = btn.dataset.username || '';
            form.role.value = btn.dataset.role || 'mahasiswa';
            form.nim.value = btn.dataset.nim || '';
            form.nidn_nip.value = btn.dataset.nidn || '';
            title.textContent = 'Edit Pengguna';
            btnSaveAndNew?.classList.add('hidden');
            openModal();
        });
    });

    const inputCsv = document.getElementById('inputCsv');
    const btnImport = document.getElementById('btnImportCsv');
    btnImport?.addEventListener('click', () => inputCsv?.click());
    inputCsv?.addEventListener('change', () => {
        if (inputCsv.files.length > 0) {
            document.getElementById('formImportCsv').submit();
        }
    });

    const params = new URLSearchParams(window.location.search);
    if (params.get('open') === 'create') {
        resetForm();
        openModal();
        params.delete('open');
        const next = params.toString();
        window.history.replaceState({}, '', window.location.pathname + (next ? ('?' + next) : ''));
    }
})();
</script>
