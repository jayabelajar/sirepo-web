<?php $base = rtrim($base_url ?? '', '/'); ?>

<div class="relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-96 h-96 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-72 h-72 bg-emerald-100 rounded-full blur-3xl opacity-30"></div>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            <!-- Left Content: Text & CTA -->
            <div class="space-y-8 text-center lg:text-left">
                <div class="space-y-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                        Aplikasi Mobile Tersedia
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">
                        Akses Repositori dalam <br class="hidden sm:block"> <span class="text-emerald-600">Genggaman Anda</span>
                    </h1>
                    <p class="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0">
                        Nikmati kemudahan mencari, membaca, dan mengunduh publikasi ilmiah Institut Agama Islam Hasan Jufri kapan saja dan di mana saja melalui aplikasi mobile resmi kami.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="<?= $download_link ?>" target="_blank" rel="noopener"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all transform hover:-translate-y-1 shadow-xl hover:shadow-emerald-200/50">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.5,13.5L12,19L6.5,13.5L7.91,12.08L11,15.17V5H13V15.17L16.09,12.08L17.5,13.5Z" />
                        </svg>
                        Unduh via Google Drive
                    </a>
                    <a href="#cara-instal" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl border border-slate-200 bg-white text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                        Cara Instalasi
                    </a>
                </div>

                <!-- App Features Badges -->
                <div class="pt-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="flex items-center gap-2 text-sm text-slate-500 justify-center lg:justify-start">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Pencarian Cepat
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500 justify-center lg:justify-start">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Baca Offline
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500 justify-center lg:justify-start">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Notifikasi Terbaru
                    </div>
                </div>
            </div>

            <!-- Right Content: Image Mockup -->
            <div class="relative flex justify-center items-center">
                <div class="w-72 h-72 sm:w-96 sm:h-96 bg-emerald-500/10 rounded-full absolute animate-pulse"></div>
                <img src="<?= $base ?>/assets/img/mobile_app.png" alt="Mobile App Mockup" 
                     class="relative w-full max-w-[320px] sm:max-w-[400px] drop-shadow-2xl z-10 hover:scale-105 transition-transform duration-500">
            </div>
        </div>
    </section>
</div>

<!-- Installation Guide Section -->
<section id="cara-instal" class="bg-white py-16 sm:py-24 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Panduan Instalasi</h2>
            <p class="text-slate-500 max-w-xl mx-auto text-sm sm:text-base">
                Ikuti langkah-langkah mudah di bawah ini untuk memasang aplikasi di perangkat Android Anda.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            <!-- Step 1 -->
            <div class="relative p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors group">
                <div class="absolute -top-5 left-8 w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-lg shadow-emerald-200">1</div>
                <div class="space-y-4 pt-4">
                    <h3 class="text-lg font-bold text-slate-900">Unduh File APK</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Klik tombol unduh di atas untuk mendapatkan file instalasi (.apk) dari folder Google Drive resmi kami.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors group">
                <div class="absolute -top-5 left-8 w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-lg shadow-emerald-200">2</div>
                <div class="space-y-4 pt-4">
                    <h3 class="text-lg font-bold text-slate-900">Aktifkan Sumber Tidak Dikenal</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Masuk ke <span class="font-semibold text-slate-700">Pengaturan > Keamanan</span>, lalu aktifkan opsi <span class="italic">"Unknown Sources"</span> atau <span class="italic">"Install Unknown Apps"</span>.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors group">
                <div class="absolute -top-5 left-8 w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-lg shadow-emerald-200">3</div>
                <div class="space-y-4 pt-4">
                    <h3 class="text-lg font-bold text-slate-900">Instal & Selesai</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Buka file APK yang telah diunduh, klik <span class="font-semibold text-slate-700">Instal</span>, dan aplikasi siap digunakan setelah proses selesai.
                    </p>
                </div>
            </div>
        </div>

        <!-- Warning Box -->
        <div class="mt-16 p-6 rounded-2xl bg-amber-50 border border-amber-100 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="text-amber-800 font-bold text-sm">Penting</h4>
                <p class="text-amber-700 text-xs sm:text-sm mt-1">
                    Aplikasi ini dikembangkan khusus untuk sistem operasi Android. Pastikan Anda mengunduh dari tautan resmi untuk menjamin keamanan data Anda.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Simple Footer CTA -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="rounded-3xl bg-emerald-600 text-white p-8 sm:p-12 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
        <div class="relative z-10 space-y-6">
            <h2 class="text-2xl sm:text-3xl font-extrabold">Mulai Riset Anda Hari Ini</h2>
            <p class="text-emerald-50 max-w-2xl mx-auto text-sm sm:text-base">
                Bergabunglah dengan ribuan mahasiswa dan akademisi lainnya yang telah menggunakan Repositori IAI Hasan Jufri.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
                <a href="<?= $download_link ?>" target="_blank" rel="noopener"
                    class="px-8 py-4 rounded-xl bg-white text-emerald-700 font-bold hover:bg-emerald-50 transition-colors shadow-lg">
                    Unduh Sekarang
                </a>
                <a href="<?= $base ?>/telusuri/"
                    class="px-8 py-4 rounded-xl bg-emerald-700 text-white font-bold hover:bg-emerald-800 transition-colors border border-emerald-500">
                    Telusuri Web
                </a>
            </div>
        </div>
    </div>
</section>
