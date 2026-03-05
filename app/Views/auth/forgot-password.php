<?php
use App\Core\Security\Csrf;

$token = Csrf::token();
$base = rtrim($base_url ?? '', '/');
$role = ($role ?? 'mahasiswa') === 'dosen' ? 'dosen' : 'mahasiswa';
$roleLabel = $role === 'dosen' ? 'Dosen' : 'Mahasiswa';
$formAction = $role === 'dosen' ? '/lupa-password/dosen' : '/lupa-password/mahasiswa';
$loginPath = $role === 'dosen' ? '/login/dosen' : '/login/mahasiswa';
$error = $_GET['error'] ?? null;
$status = $_GET['status'] ?? null;

$errors = [
    'csrf' => 'Sesi keamanan berakhir. Muat ulang halaman lalu coba lagi.',
    'email' => 'Masukkan alamat email yang valid.',
    'mail' => 'Pengiriman email gagal. Periksa konfigurasi SMTP terlebih dahulu.',
];
?>

<div class="w-full max-w-[420px] bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-6">
    <div class="space-y-2 text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Lupa Password <?= e($roleLabel) ?></h1>
        <p class="text-sm text-slate-600">
            Masukkan email akun Anda. Tautan pengaturan ulang password akan dikirim ke email tersebut.
        </p>
    </div>

    <?php if ($status === 'sent'): ?>
        <div class="text-sm px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
            Jika email terdaftar, tautan reset password telah dikirim.
        </div>
    <?php endif; ?>

    <?php if ($error && isset($errors[$error])): ?>
        <div class="text-sm px-4 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200">
            <?= e($errors[$error]) ?>
        </div>
    <?php endif; ?>

    <form action="<?= $base . $formAction ?>" method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Email</label>
            <input
                type="email"
                name="email"
                required
                autocomplete="email"
                placeholder="nama@inhafi.ac.id"
                class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
            />
        </div>

        <button
            type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
            Kirim Tautan Reset
        </button>
    </form>

    <p class="text-sm text-center text-slate-600">
        Kembali ke
        <a href="<?= $base . $loginPath ?>" class="font-semibold text-emerald-600 hover:underline">
            halaman login <?= strtolower($roleLabel) ?>
        </a>
    </p>
</div>
