<?php
use App\Core\Security\Csrf;

$token = Csrf::token();
$base = rtrim($base_url ?? '', '/');
$tokenValue = $token_value ?? '';
$isValidToken = (bool) ($is_valid_token ?? false);
$role = ($role ?? 'mahasiswa') === 'dosen' ? 'dosen' : 'mahasiswa';
$loginPath = $role === 'dosen' ? '/login/dosen' : '/login/mahasiswa';
$error = $_GET['error'] ?? null;

$errors = [
    'csrf'  => 'Sesi keamanan berakhir. Muat ulang halaman lalu coba lagi.',
    'token' => 'Tautan reset tidak valid atau sudah kedaluwarsa.',
    'empty' => 'Password baru dan konfirmasi wajib diisi.',
    'weak'  => 'Password minimal harus 8 karakter.',
    'nomatch' => 'Konfirmasi password tidak sesuai.',
];
?>

<div class="w-full px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <div class="h-1.5 w-full bg-slate-100">
            <div class="h-full bg-emerald-600 w-1/2 transition-all duration-500"></div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    Atur Ulang Password
                    <span class="text-xl">🔐</span>
                </h1>
                <p class="text-slate-500 mt-2 text-sm leading-relaxed">
                    Masukkan password baru untuk melanjutkan akses akun Anda. Pastikan password sulit ditebak agar akun tetap aman.
                </p>
            </div>

            <?php if ($error && isset($errors[$error])): ?>
                <div class="text-sm px-4 py-3 rounded-xl border <?= $error === 'token' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                    <?= e($errors[$error]) ?>
                </div>
            <?php endif; ?>

            <?php if (!$isValidToken): ?>
                <div class="text-sm px-4 py-3 rounded-xl bg-amber-50 text-amber-700 border border-amber-200">
                    Tautan reset password tidak valid atau sudah kedaluwarsa.
                </div>
                <div class="text-center text-sm">
                    <a href="<?= $base . $loginPath ?>" class="text-slate-500 hover:text-emerald-600 font-medium inline-flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Login
                    </a>
                </div>
            <?php else: ?>
                <form action="<?= $base . '/reset-password/' . rawurlencode((string) $tokenValue) ?>" method="POST" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Masukkan password baru"
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all duration-200 outline-none text-slate-900 placeholder:text-slate-400"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Ulangi password baru"
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all duration-200 outline-none text-slate-900 placeholder:text-slate-400"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-xl transition-all duration-300 shadow-lg shadow-emerald-100 flex items-center justify-center gap-2 group">
                        Simpan Password Baru
                        <svg class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <div class="text-center">
                    <a href="<?= $base . $loginPath ?>" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
