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

$showGlobalError = $error && isset($errors[$error]) && !(!$isValidToken && $error === 'token');
?>

<style>
    :root { color-scheme: light; }
    body.dark { background: #0f172a; color: #e2e8f0; }
    body.dark .login-card { background: #0b1220; border-color: #1f2937; color: #e2e8f0; }
    body.dark .login-card h1, body.dark .login-card p, body.dark .login-card label, body.dark .login-card span, body.dark .login-card a { color: #e2e8f0 !important; }
    body.dark .login-card .text-gray-900, body.dark .login-card .text-gray-800, body.dark .login-card .text-gray-700 { color: #f8fafc !important; }
    body.dark .login-card .text-gray-600, body.dark .login-card .text-gray-500, body.dark .login-card .text-gray-400 { color: #cbd5e1 !important; }
    body.dark .login-card .bg-gray-50, body.dark .login-card .bg-white { background: #111827 !important; }
    body.dark .login-card input { color: #f8fafc !important; border-color: #1f2937 !important; }
    body.dark .login-card input::placeholder { color: #94a3b8 !important; }
    body.dark .login-card .bg-red-50 { background-color: #3b0d0d !important; color: #fecdd3 !important; }
    body.dark .login-card .bg-amber-50 { background-color: #422006 !important; color: #fef08a !important; }
</style>

<div class="w-full max-w-[400px] md:max-w-md bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-6 mx-4 sm:mx-0 transition-all duration-300 relative login-card overflow-hidden">
    <div class="absolute -top-12 -left-16 w-40 h-40 bg-emerald-100 rounded-full blur-3xl opacity-70"></div>
    <div class="absolute -bottom-14 -right-16 w-48 h-48 bg-emerald-50 rounded-full blur-3xl opacity-70"></div>

    <div class="relative flex justify-center">
        <img src="<?= $base; ?>/assets/img/image.png" alt="Ilustrasi Reset Password" class="w-32 drop-shadow-xl mb-3">
    </div>

    <div class="relative text-center space-y-2">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Konfirmasi Ganti Password</h1>
        <p class="text-sm text-gray-500">Masukkan password baru dan konfirmasi password untuk menyelesaikan proses reset akun.</p>
    </div>

    <?php if ($showGlobalError): ?>
        <?php $isWarning = $error === 'token'; ?>
        <div class="text-sm px-4 py-3 rounded-xl border <?= $isWarning ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
            <?= e($errors[$error]) ?>
        </div>
    <?php endif; ?>

    <?php if (!$isValidToken): ?>
        <div class="text-sm px-4 py-3 rounded-xl border bg-amber-50 text-amber-700 border-amber-200">
            Tautan reset password tidak valid atau sudah kedaluwarsa.
        </div>

        <div class="text-center text-sm text-gray-500">
            <a href="<?= $base . $loginPath ?>" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman login
            </a>
        </div>
    <?php else: ?>
        <form action="<?= $base . '/reset-password/' . rawurlencode((string) $tokenValue) ?>" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Password Baru</label>
                <div class="relative">
                    <input
                        type="password"
                        id="newPasswordInput"
                        name="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Masukkan password baru"
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 pr-12"
                    />
                    <button type="button" id="toggleNewPasswordBtn" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none">
                        <svg id="iconNewEyeOpen" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="iconNewEyeClosed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="confirmPasswordInput"
                        name="password_confirmation"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Ulangi password baru"
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 pr-12"
                    />
                    <button type="button" id="toggleConfirmPasswordBtn" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none">
                        <svg id="iconConfirmEyeOpen" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="iconConfirmEyeClosed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 transform active:scale-[0.98] flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Password Baru
            </button>
        </form>

        <div class="text-center text-sm text-gray-500">
            <a href="<?= $base . $loginPath ?>" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman login
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
(function () {
    const key = 'sirepo-theme';
    const saved = localStorage.getItem(key);
    if (saved === 'dark') {
        document.body.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
    }
})();

(function () {
    const setupToggle = (buttonId, inputId, openId, closedId) => {
        const button = document.getElementById(buttonId);
        const field = document.getElementById(inputId);
        const iconOpen = document.getElementById(openId);
        const iconClosed = document.getElementById(closedId);

        if (!button || !field || !iconOpen || !iconClosed) {
            return;
        }

        button.addEventListener('click', () => {
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            iconOpen.classList.toggle('hidden', !isPassword);
            iconClosed.classList.toggle('hidden', isPassword);
            field.focus();
        });
    };

    setupToggle('toggleNewPasswordBtn', 'newPasswordInput', 'iconNewEyeOpen', 'iconNewEyeClosed');
    setupToggle('toggleConfirmPasswordBtn', 'confirmPasswordInput', 'iconConfirmEyeOpen', 'iconConfirmEyeClosed');
})();
</script>
