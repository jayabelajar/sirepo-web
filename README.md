# SIREPO Web

SIREPO Web adalah aplikasi repositori digital untuk mengelola, menelusuri, dan mempublikasikan karya ilmiah Institut Agama Islam Hasan Jufri. Aplikasi ini dibangun dengan PHP native bergaya MVC, MySQL, Tailwind CSS, dan endpoint API untuk integrasi mobile.

## Fitur Utama

- Portal publik untuk pencarian repository, kategori, detail karya, dan unduhan PDF.
- Dashboard mahasiswa dan dosen untuk pengelolaan repository masing-masing.
- Dashboard admin dan developer untuk moderasi repository, master data, pengguna, pengaturan, backup, dan log aktivitas.
- API JSON berbasis bearer token untuk aplikasi mobile.
- Autentikasi berbasis role, CSRF protection untuk form web, rate limit login/API, dan validasi upload PDF.

## Kebutuhan Sistem

- PHP 8.1 atau lebih baru.
- MySQL/MariaDB.
- Node.js 18 atau lebih baru untuk build Tailwind CSS.
- Web server Apache/Nginx dengan document root disarankan ke folder `public`.

## Instalasi Lokal

1. Salin konfigurasi environment:

   ```bash
   cp .env.example .env
   ```

2. Isi nilai penting di `.env`:

   ```env
   APP_BASE_URL=http://localhost:8000
   APP_ENV=local
   APP_API_SECRET=isi-dengan-string-random-panjang
   APP_TRUSTED_HOSTS=localhost,127.0.0.1
   API_CORS_ORIGINS=*

   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=sirepo
   DB_USER=root
   DB_PASS=
   ```

3. Pasang dependency frontend dan build asset:

   ```bash
   npm install
   npm run build
   ```

4. Jalankan server lokal:

   ```bash
   npm run serve
   ```

5. Buka aplikasi di `http://localhost:8000`.

## Perintah NPM

- `npm run dev` menjalankan Tailwind CSS dalam mode watch.
- `npm run build` menghasilkan CSS minified ke `public/assets/css/tailwind.css`.
- `npm run serve` menjalankan PHP built-in server pada `localhost:8000`.

## Konfigurasi Keamanan

- Jangan commit file `.env`, kredensial database, secret API, dump database, log, atau file upload.
- Gunakan `APP_API_SECRET` yang panjang dan acak di production.
- Isi `APP_BASE_URL` dengan domain production yang benar.
- Isi `APP_TRUSTED_HOSTS` dengan daftar host resmi, dipisahkan koma, untuk menekan risiko host-header injection.
- Batasi `API_CORS_ORIGINS` ke origin aplikasi yang dipercaya di production, misalnya `https://mobile.example.ac.id`.
- Arahkan document root web server ke `public` bila memungkinkan.
- Pastikan direktori `storage` dapat ditulis oleh PHP, tetapi tidak diekspos sebagai static public directory.
- Gunakan HTTPS di production agar cookie session memakai mode secure.
- Jalankan audit dependency runtime secara berkala:

   ```bash
   npm audit --omit=dev
   ```

## Deployment Singkat

1. Upload source aplikasi ke server.
2. Pastikan document root mengarah ke `public`.
3. Buat `.env` production berdasarkan `.env.example`.
4. Jalankan `npm ci` lalu `npm run build` di lingkungan build/deploy.
5. Pastikan permission `storage` dan `tmp` writable untuk user web server.
6. Konfigurasi database dan import schema/data awal sesuai kebutuhan.
7. Uji login, upload PDF, unduhan PDF, dan endpoint API setelah deploy.

## API

Semua response API menggunakan JSON. Endpoint autentikasi utama:

- `POST /api/login`
- `POST /api/mobile/login/mahasiswa`
- `POST /api/mobile/login/dosen`
- `POST /api/logout`
- `GET /api/me`

Endpoint yang membutuhkan autentikasi menerima header:

```http
Authorization: Bearer <token>
```

## Troubleshooting

- Jika halaman tidak ditemukan, cek konfigurasi rewrite web server dan nilai `APP_BASE_URL`.
- Jika login API gagal karena token, pastikan `APP_API_SECRET` terisi dan konsisten.
- Jika upload PDF gagal, cek ukuran file, tipe file, dan permission direktori `storage/uploads`.
- Jika CSS tidak berubah, jalankan ulang `npm run build` dan pastikan file hasil build ter-deploy.

## Lisensi

Proyek ini digunakan untuk kebutuhan internal institusi. Atur lisensi dan kebijakan distribusi sesuai keputusan pemilik aplikasi.
