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
    'csrf' => 'Sesi keamanan berakhir. Muat ulang halaman lalu coba lagi.',
    'token' => 'Tautan reset tidak valid atau sudah kedaluwarsa.',
    'empty' => 'Password baru dan konfirmasi wajib diisi.',
    'weak' => 'Password minimal harus 8 karakter.',
    'nomatch' => 'Konfirmasi password tidak sesuai.',
];
?>

<div class="w-full max-w-[420px] bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-6">
    <div class="space-y-2 text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Atur Ulang Password</h1>
        <p class="text-sm text-slate-600">Masukkan password baru untuk melanjutkan akses akun Anda.</p>
    </div>

    <?php if ($error && isset($errors[$error])): ?>
        <div class="text-sm px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200">
            <?= e($errors[$error]) ?>
        </div>
    <?php endif; ?>

    <?php if (!$isValidToken): ?>
        <div class="text-sm px-4 py-3 rounded-xl bg-amber-50 text-amber-700 border border-amber-200">
            Tautan reset password tidak valid atau sudah kedaluwarsa.
        </div>
        <p class="text-sm text-center text-slate-600">
            Silakan kembali ke
            <a href="<?= $base . $loginPath ?>" class="font-semibold text-emerald-600 hover:underline">
                halaman login
            </a>
            lalu ajukan ulang permintaan reset.
        </p>
    <?php else: ?>
        <form action="<?= $base . '/reset-password/' . rawurlencode((string) $tokenValue) ?>" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Password Baru</label>
                <input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                />
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                />
            </div>

            <button
                type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
                Simpan Password Baru
            </button>
        </form>
    <?php endif; ?>
</div>
