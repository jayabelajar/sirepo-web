<?php
use App\Core\Security\Csrf;

$token = Csrf::token();
$base  = rtrim($base_url ?? '', '/');
$role  = ($role ?? 'mahasiswa') === 'dosen' ? 'dosen' : 'mahasiswa';
$roleLabel = $role === 'dosen' ? 'Dosen' : 'Mahasiswa';
$formAction = $role === 'dosen' ? '/lupa-password/dosen' : '/lupa-password/mahasiswa';
$loginPath  = $role === 'dosen' ? '/login/dosen' : '/login/mahasiswa';
$error  = $_GET['error'] ?? null;
$status = $_GET['status'] ?? null;

$errorMessages = [
    'csrf'  => 'Sesi keamanan berakhir. Muat ulang halaman lalu coba lagi.',
    'email' => 'Masukkan alamat email yang valid.',
    'mail'  => 'Pengiriman email gagal. Periksa konfigurasi SMTP terlebih dahulu.',
];
?>

<style>
    :root { color-scheme: light; }
    body.dark { background: #0f172a; color: #e2e8f0; }
    body.dark .login-card { background: #0b1220; border-color: #1f2937; color: #e2e8f0; }
    body.dark .login-card h1, body.dark .login-card p, body.dark .login-card label, body.dark .login-card span, body.dark .login-card a { color: #e2e8f0 !important; }
    body.dark .login-card .text-gray-900, body.dark .login-card .text-gray-800, body.dark .login-card .text-gray-700 { color: #f8fafc !important; }
</style>

<div class="w-full max-w-[400px] md:max-w-md bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-6 transition-all duration-300 relative login-card">
    <div class="flex justify-center">
        <img src="<?= $base; ?>/assets/img/image.png" alt="Ilustrasi Pemulihan" class="w-32 drop-shadow-xl mb-3">
    </div>

    <div class="text-center space-y-2">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Lupa Password</h1>
        <p class="text-sm text-gray-500">Masukkan email akun Anda. Kami akan mengirimkan tautan pengaturan ulang password.</p>
    </div>

    <?php if ($status === 'sent'): ?>
        <div class="text-sm px-4 py-3 rounded-xl border bg-emerald-50 text-emerald-700 border-emerald-200">
            Jika email terdaftar, instruksi reset password telah dikirim.
        </div>
    <?php endif; ?>

    <?php if ($error && isset($errorMessages[$error])): ?>
        <div class="text-sm px-4 py-3 rounded-xl border bg-red-50 text-red-700 border-red-200">
            <?= e($errorMessages[$error]) ?>
        </div>
    <?php endif; ?>

    <form action="<?= $base . $formAction ?>" method="POST" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= e($token); ?>">

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Email</label>
            <input
                type="email"
                name="email"
                required
                autocomplete="email"
                placeholder="nama@inhafi.ac.id"
                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
            />
        </div>

        <button
            type="submit"
            class="btn-login w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-emerald-500/40 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            Kirim Tautan Reset
        </button>
    </form>

    <div class="text-center text-sm text-gray-500">
        <a href="<?= $base . $loginPath ?>" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
            Kembali ke halaman login
        </a>
    </div>
</div>
