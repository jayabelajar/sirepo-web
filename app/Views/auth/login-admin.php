<?php
use App\Core\Security\Csrf;

$token = Csrf::token();
$base  = rtrim($base_url ?? '', '/');
$error = $_GET['error'] ?? null;

$errorMessages = [
    'invalid' => 'Email/username atau password tidak valid.',
    'notfound' => 'Akun tidak ditemukan.',
    'banned' => 'Akun diblokir sementara. Hubungi super admin.',
    'throttled' => 'Terlalu banyak percobaan login. Coba kembali beberapa menit lagi.',
    'csrf' => 'Sesi keamanan berakhir. Muat ulang halaman lalu coba lagi.',
    'empty' => 'Email/username dan password wajib diisi.',
];
?>

<style>
    :root { color-scheme: light; }
    body.dark { background: #0f172a; color: #e2e8f0; }
    body.dark .login-card { background: #0b1220; border-color: #1f2937; color: #e2e8f0; }
    body.dark .login-card h1, body.dark .login-card p, body.dark .login-card label, body.dark .login-card span, body.dark .login-card a { color: #e2e8f0 !important; }
    body.dark .login-card .text-gray-900, body.dark .login-card .text-gray-800, body.dark .login-card .text-gray-700 { color: #f8fafc !important; }
    body.dark .login-card .text-gray-600, body.dark .login-card .text-gray-500, body.dark .login-card .text-gray-400 { color: #cbd5e1 !important; }
    body.dark .login-card .bg-gray-50, body.dark .login-card .bg-white { background: #111827 !important; }
    body.dark .login-card input, body.dark .login-card select { color: #f8fafc !important; border-color: #1f2937 !important; }
    body.dark .login-card input::placeholder { color: #94a3b8 !important; }
    body.dark .login-card .bg-red-50 { background-color: #3b0d0d !important; color: #fecdd3 !important; }
    body.dark .login-card .bg-amber-50 { background-color: #422006 !important; color: #fef08a !important; }
    body.dark .login-card .bg-emerald-600 { background-color: #10b981 !important; }
    body.dark .login-card .hover\:bg-emerald-700:hover { background-color: #059669 !important; }
    body.dark .toggle-theme { color: #cbd5e1; }
    body.dark .btn-login { box-shadow: none !important; }
    body.dark .btn-login:hover { box-shadow: none !important; }
</style>

<div class="w-full max-w-[400px] md:max-w-md bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-6 transition-all duration-300 relative login-card">
    <button id="themeToggle" class="toggle-theme absolute right-4 top-4 p-2 rounded-full hover:bg-gray-100 transition-colors" aria-label="Toggle tema">
        <svg id="iconSun" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m14.95 7.95L17.5 17.5M6.5 6.5 5.05 5.05M17.5 6.5l1.45-1.45M6.5 17.5 5.05 18.95M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
        <svg id="iconMoon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12.41A8 8 0 1111.59 4 6 6 0 0020 12.41z"/></svg>
    </button>

    <div class="text-center space-y-2">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Login Admin</h1>
        <p class="text-sm text-gray-500">Masuk untuk mengelola data pengguna, repository, dan pengaturan sistem.</p>
    </div>

    <?php if ($error && isset($errorMessages[$error])): ?>
        <?php $isWarning = in_array($error, ['banned', 'throttled'], true); ?>
        <div class="text-sm px-4 py-3 rounded-xl border <?= $isWarning ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
            <?= e($errorMessages[$error]) ?>
        </div>
    <?php endif; ?>

    <div id="geoError" class="hidden text-sm px-4 py-3 rounded-xl bg-amber-50 text-amber-700 border border-amber-200"></div>

    <form action="<?= $base; ?>/login/admin" method="POST" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= e($token); ?>">
        <input type="hidden" name="latitude" id="loginLat" value="">
        <input type="hidden" name="longitude" id="loginLng" value="">

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Email atau Username</label>
            <input
                type="text"
                name="email"
                required
                autocomplete="username"
                placeholder="admin@inhafi.ac.id"
                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
            />
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Password</label>
            <div class="relative group">
                <input
                    type="password"
                    id="adminPasswordInput"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="********"
                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 pr-12"
                />
                <button type="button" id="togglePasswordBtn" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none">
                    <svg id="iconEyeOpen" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="iconEyeClosed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="rememberMe" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                <label for="rememberMe" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">Ingat Saya</label>
            </div>
            <span class="text-sm text-gray-500">Reset password melalui super admin.</span>
        </div>

        <button
            type="submit"
            class="btn-login w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 transform active:scale-[0.98] flex justify-center items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Masuk
        </button>
    </form>

    <div class="pt-2 border-t border-gray-100">
        <p class="text-xs text-center text-gray-400 leading-relaxed">
            Akses terbatas untuk administrator yang berwenang.
        </p>
    </div>
</div>

<script>
// 1. Toggle Password
document.getElementById('togglePasswordBtn')?.addEventListener('click', function () {
    const input = document.getElementById('adminPasswordInput');
    const eyeOpen = document.getElementById('iconEyeOpen');
    const eyeClosed = document.getElementById('iconEyeClosed');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    } else {
        input.type = 'password';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    }
    input.focus();
});

// 2. Geolocation
(function () {
    const latInput = document.getElementById('loginLat');
    const lngInput = document.getElementById('loginLng');
    const form = document.querySelector('form[action*="/login/admin"]');
    const geoError = document.getElementById('geoError');
    let submitting = false;

    const requestGeo = (onSuccess, onFail) => {
        if (!navigator.geolocation) {
            onFail?.('Browser tidak mendukung lokasi.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                if (latInput) latInput.value = pos.coords.latitude.toFixed(6);
                if (lngInput) lngInput.value = pos.coords.longitude.toFixed(6);
                geoError?.classList.add('hidden');
                onSuccess?.();
            },
            () => onFail?.('Lokasi tidak tersedia. Anda tetap dapat melanjutkan login.'),
            { enableHighAccuracy: true, timeout: 7000, maximumAge: 0 }
        );
    };

    requestGeo();

    form?.addEventListener('submit', (event) => {
        if (submitting) return;
        if (!(latInput?.value && lngInput?.value)) {
            event.preventDefault();
            requestGeo(
                () => {
                    submitting = true;
                    form.submit();
                },
                (msg) => {
                    if (geoError) {
                        geoError.textContent = msg;
                        geoError.classList.remove('hidden');
                    }
                    submitting = true;
                    form.submit();
                }
            );
        }
    });
})();

// 3. Theme toggle
(function() {
    const key = 'sirepo-theme';
    const btn = document.getElementById('themeToggle');
    const sun = document.getElementById('iconSun');
    const moon = document.getElementById('iconMoon');
    const saved = localStorage.getItem(key);
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const mode = saved || (prefersDark ? 'dark' : 'light');
    const apply = (m) => {
        if (m === 'dark') {
            document.body.classList.add('dark');
            sun?.classList.add('hidden');
            moon?.classList.remove('hidden');
        } else {
            document.body.classList.remove('dark');
            sun?.classList.remove('hidden');
            moon?.classList.add('hidden');
        }
        document.documentElement.style.colorScheme = m;
        localStorage.setItem(key, m);
    };
    apply(mode);
    btn?.addEventListener('click', () => {
        const next = document.body.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
    });
})();
</script>
