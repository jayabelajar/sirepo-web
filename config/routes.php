<?php

use App\Core\Router;

/* PUBLIC controllers */
use App\Controllers\Public\{
    HomeController,
    TentangController,
    TelusuriController,
    RepositoryController,
    FileDownloadController,
    SitemapController,
    RobotsController,
    HalamanController,
    RegisterController,
    MaintenanceController,
    DownloadController
};

/* AUTH controllers */
use App\Controllers\Auth\AuthMahasiswaController;
use App\Controllers\Auth\AuthDosenController;
use App\Controllers\Auth\AuthAdminController;
use App\Controllers\Auth\AuthDeveloperController;
use App\Controllers\Auth\ForgotPasswordController;

/* DASHBOARD MAHASISWA */
use App\Controllers\Mahasiswa\DashboardController as MahasiswaDashboard;
use App\Controllers\Mahasiswa\BookmarkController as MahasiswaBookmark;
use App\Controllers\Mahasiswa\MyRepositoryController as MahasiswaMyRepository;
use App\Controllers\Mahasiswa\ProfilController as MahasiswaProfil;
use App\Controllers\Mahasiswa\TelusuriController as MahasiswaTelusuri;
use App\Controllers\Mahasiswa\ActivityController as MahasiswaActivity;

use App\Controllers\BookmarkController;

/* DASHBOARD DOSEN */
use App\Controllers\Dosen\DashboardController as DosenDashboard;
use App\Controllers\Dosen\MyRepositoryController as DosenMyRepository;
use App\Controllers\Dosen\BookmarkController as DosenBookmark;
use App\Controllers\Dosen\ActivityController as DosenActivity;
use App\Controllers\Dosen\TelusuriController as DosenTelusuri;
use App\Controllers\Dosen\ProfilController as DosenProfil;

/* DASHBOARD ADMIN */
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Admin\ProgramStudiController as AdminProgramStudi;
use App\Controllers\Admin\RepositoryController as AdminRepository;
use App\Controllers\Admin\MataKuliahController as AdminMataKuliah;
use App\Controllers\Admin\UserController as AdminUser;
use App\Controllers\Admin\ProfileController as AdminProfile;
use App\Controllers\Admin\SettingController as AdminSetting;
use App\Controllers\Admin\LogController as AdminLog;
use App\Controllers\Developer\DashboardController as DeveloperDashboard;
use App\Controllers\Developer\ProfileController as DeveloperProfile;
use App\Models\Setting;

/* API controllers */
use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\RepositoryApiController;
use App\Controllers\Api\LookupApiController;
use App\Controllers\Api\BookmarkApiController;
use App\Controllers\Api\MobileAdminApiController;
use App\Controllers\Api\ProfileApiController;
use App\Controllers\Api\ForgotPasswordApiController;


/* init router */
$router = new Router();

// Maintenance gate untuk halaman publik
$settingModel = new Setting();
$setting = $settingModel->get();
$isMaintenance = !empty($setting['maintenance_mode']);
$config = require __DIR__ . '/config.php';
$redirectTo = static function (string $targetPath) use ($config): void {
    $base = rtrim($config['base_url'] ?? '', '/');
    header('Location: ' . $base . '/' . ltrim($targetPath, '/'));
    exit;
};
$basePath = rtrim(parse_url($config['base_url'] ?? '', PHP_URL_PATH) ?? '', '/');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
    if ($requestPath === false) $requestPath = '/';
}

$isAdminPath = strpos($requestPath, '/admin') === 0
    || strpos($requestPath, '/developer') === 0
    || strpos($requestPath, '/__admin') === 0
    || strpos($requestPath, '/__developer') === 0
    || strpos($requestPath, '/__dosen') === 0
    || strpos($requestPath, '/login/admin') === 0
    || strpos($requestPath, '/login/developer') === 0
    || strpos($requestPath, '/login/dosen') === 0
    || strpos($requestPath, '/lupa-password/dosen') === 0
    || strpos($requestPath, '/api') === 0;

if ($isMaintenance && !$isAdminPath && strpos($requestPath, '/maintenance') !== 0) {
    $base = rtrim($config['base_url'] ?? '', '/');
    header('Location: ' . $base . '/maintenance');
    exit;
}

/* ============================================================
   PUBLIC PAGES
============================================================ */
$router->get('/',                    [HomeController::class, 'index']);
$router->get('/tentang',             [TentangController::class, 'index']);
$router->get('/faq',                 [HalamanController::class, 'faq']);
$router->get('/panduan',             [HalamanController::class, 'panduan']);
$router->get('/kebijakan',           [HalamanController::class, 'privasi']);
$router->get('/syarat',              [HalamanController::class, 'ketentuan']);
$router->get('/kontak',              [HalamanController::class, 'kontak']);
$router->get('/download',            [DownloadController::class, 'index']);

/* telusuri */
$router->get('/telusuri',                        [TelusuriController::class, 'index']);
$router->get('/telusuri/',                       [TelusuriController::class, 'index']);
$router->get('/telusuri/year',                   [TelusuriController::class, 'byYear']);
$router->get('/telusuri/year/{tahun}',           [TelusuriController::class, 'byYearDetail']);

$router->get('/telusuri/program-studi',          [TelusuriController::class, 'byProgramStudi']);
$router->get('/telusuri/program-studi/{slug}',   [TelusuriController::class, 'byProgramStudiDetail']);

$router->get('/telusuri/mata-kuliah',            [TelusuriController::class, 'byMataKuliah']);
$router->get('/telusuri/mata-kuliah/{kode}',     [TelusuriController::class, 'byMataKuliahDetail']);

$router->get('/telusuri/author',                 [TelusuriController::class, 'byAuthor']);
$router->get('/telusuri/author/{username}',      [TelusuriController::class, 'byAuthorDetail']);

$router->get('/telusuri/jenis-karya',            [TelusuriController::class, 'jenisKarya']);
$router->get('/telusuri/jenis-karya/{jenis}',    [TelusuriController::class, 'jenisKaryaDetail']);

/* detail repository */
$router->get('/repository/{slug}',               [RepositoryController::class, 'detail']);
$router->get('/repository/{slug}/download',      [FileDownloadController::class, 'repository']);
$router->get('/robots.txt',                      [RobotsController::class, 'index']);
$router->get('/robots.txt/',                     [RobotsController::class, 'index']);
$router->get('/robot.txt', static function () use ($config): void {
    $base = rtrim($config['base_url'] ?? '', '/');
    header('Location: ' . $base . '/robots.txt', true, 301);
    exit;
});
$router->get('/robot.txt/', static function () use ($config): void {
    $base = rtrim($config['base_url'] ?? '', '/');
    header('Location: ' . $base . '/robots.txt', true, 301);
    exit;
});
$router->get('/sitemap.xml',                     [SitemapController::class, 'index']);
$router->get('/sitemap.xml/',                    [SitemapController::class, 'index']);


/* ============================================================
   AUTH MAHASISWA
============================================================ */
$router->get('/login/mahasiswa',     [AuthMahasiswaController::class, 'showLoginForm']);
$router->post('/login/mahasiswa',    [AuthMahasiswaController::class, 'login']);
$router->get('/login', static function () use ($redirectTo): void {
    $redirectTo('/login/mahasiswa');
});
$router->post('/login',              [AuthMahasiswaController::class, 'login']); // backward compatibility
$router->post('/logout',             [AuthMahasiswaController::class, 'logout']);


/* ============================================================
   AUTH DOSEN
============================================================ */
$router->get('/login/dosen',         [AuthDosenController::class, 'showLoginForm']);
$router->post('/login/dosen',        [AuthDosenController::class, 'login']);
$router->get('/__dosen/login', static function () use ($redirectTo): void {
    $redirectTo('/login/dosen');
});
$router->post('/__dosen/login',      [AuthDosenController::class, 'login']); // backward compatibility


/* ============================================================
   AUTH ADMIN
============================================================ */
$router->get('/login/admin',         [AuthAdminController::class, 'showLoginForm']);
$router->post('/login/admin',        [AuthAdminController::class, 'login']);
$router->get('/__admin/login', static function () use ($redirectTo): void {
    $redirectTo('/login/admin');
});
$router->post('/__admin/login',      [AuthAdminController::class, 'login']); // backward compatibility

/* ============================================================
   AUTH DEVELOPER
============================================================ */
$router->get('/login/developer',      [AuthDeveloperController::class, 'showLoginForm']);
$router->post('/login/developer',     [AuthDeveloperController::class, 'login']);
$router->get('/__developer/login', static function () use ($redirectTo): void {
    $redirectTo('/login/developer');
});
$router->post('/__developer/login',   [AuthDeveloperController::class, 'login']); // backward compatibility

/* ============================================================
   LUPA PASSWORD (MAHASISWA & DOSEN)
============================================================ */
$router->get('/lupa-password', static function () use ($redirectTo): void {
    $redirectTo('/lupa-password/mahasiswa');
});
$router->get('/lupa-password/mahasiswa', [ForgotPasswordController::class, 'showMahasiswaForm']);
$router->post('/lupa-password/mahasiswa', [ForgotPasswordController::class, 'submitMahasiswa']);
$router->get('/lupa-password/dosen', [ForgotPasswordController::class, 'showDosenForm']);
$router->post('/lupa-password/dosen', [ForgotPasswordController::class, 'submitDosen']);
$router->get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm']);
$router->post('/reset-password/{token}', [ForgotPasswordController::class, 'submitReset']);


/* ============================================================
   DASHBOARD MAHASISWA
============================================================ */
$router->get('/mahasiswa', static function () use ($redirectTo): void {
    $redirectTo('/mahasiswa/dashboard');
});
$router->get('/__mahasiswa', static function () use ($redirectTo): void {
    $redirectTo('/mahasiswa/dashboard');
});
$router->get('/__mahasiswa/dashboard', static function () use ($redirectTo): void {
    $redirectTo('/mahasiswa/dashboard');
});
$router->get('/mahasiswa/dashboard', [MahasiswaDashboard::class, 'index']);
$router->get('/mahasiswa/bookmarks', [MahasiswaBookmark::class, 'index']);
$router->get('/mahasiswa/my-repository', [MahasiswaMyRepository::class, 'index']);
$router->get('/mahasiswa/my-repository/create', [MahasiswaMyRepository::class, 'create']);
$router->post('/mahasiswa/my-repository/store', [MahasiswaMyRepository::class, 'store']);
$router->get('/mahasiswa/my-repository/{id}/edit', [MahasiswaMyRepository::class, 'edit']);
$router->post('/mahasiswa/my-repository/{id}/update', [MahasiswaMyRepository::class, 'update']);
$router->post('/mahasiswa/my-repository/{id}/delete', [MahasiswaMyRepository::class, 'delete']);
$router->get('/mahasiswa/profil', [MahasiswaProfil::class, 'index']);
$router->post('/mahasiswa/profil', [MahasiswaProfil::class, 'update']);
$router->get('/mahasiswa/telusuri', [MahasiswaTelusuri::class, 'redirect']);
$router->get('/mahasiswa/activity', [MahasiswaActivity::class, 'index']);
$router->post('/bookmark/{slug}/toggle', [BookmarkController::class, 'toggle']);


/* ============================================================
   DASHBOARD DOSEN
============================================================ */
$router->get('/dosen', static function () use ($redirectTo): void {
    $redirectTo('/dosen/dashboard');
});
$router->get('/__dosen', static function () use ($redirectTo): void {
    $redirectTo('/dosen/dashboard');
});
$router->get('/__dosen/dashboard', static function () use ($redirectTo): void {
    $redirectTo('/dosen/dashboard');
});
$router->get('/dosen/dashboard', [DosenDashboard::class, 'index']);
$router->get('/dosen/my-repository', [DosenMyRepository::class, 'index']);
$router->post('/dosen/my-repository', [DosenMyRepository::class, 'store']);
$router->post('/dosen/my-repository/{id}/update', [DosenMyRepository::class, 'update']);
$router->post('/dosen/my-repository/{id}/delete', [DosenMyRepository::class, 'delete']);
$router->get('/dosen/repository', [DosenMyRepository::class, 'index']);
$router->get('/dosen/repository/create', [DosenMyRepository::class, 'create']);
$router->get('/dosen/repository/{id}/edit', [DosenMyRepository::class, 'edit']);
$router->post('/dosen/repository/{id}/delete', [DosenMyRepository::class, 'delete']);
$router->get('/dosen/bookmark', [DosenBookmark::class, 'index']);
$router->get('/dosen/telusuri', [DosenTelusuri::class, 'redirect']);
$router->get('/dosen/activity', [DosenActivity::class, 'index']);
$router->get('/dosen/profile', [DosenProfil::class, 'index']);
$router->post('/dosen/profile', [DosenProfil::class, 'update']);


/* ============================================================
   DASHBOARD ADMIN
============================================================ */
$router->get('/admin', static function () use ($redirectTo): void {
    $redirectTo('/admin/dashboard');
});
$router->get('/__admin', static function () use ($redirectTo): void {
    $redirectTo('/admin/dashboard');
});
$router->get('/__admin/dashboard', static function () use ($redirectTo): void {
    $redirectTo('/admin/dashboard');
});
$router->get('/admin/dashboard', [AdminDashboard::class, 'index']);
$router->get('/admin/program-studi', [AdminProgramStudi::class, 'index']);
$router->post('/admin/program-studi', [AdminProgramStudi::class, 'store']);
$router->post('/admin/program-studi/{id}/update', [AdminProgramStudi::class, 'update']);
$router->post('/admin/program-studi/{id}/delete', [AdminProgramStudi::class, 'delete']);
$router->get('/admin/repository', [AdminRepository::class, 'index']);
$router->get('/admin/repository/pending', [AdminRepository::class, 'pending']);
$router->post('/admin/repository/{id}/approve', [AdminRepository::class, 'approve']);
$router->post('/admin/repository/{id}/reject', [AdminRepository::class, 'reject']);
$router->get('/admin/repository/create', [AdminRepository::class, 'create']);
$router->get('/admin/repository/{id}/edit', [AdminRepository::class, 'edit']);
$router->post('/admin/repository', [AdminRepository::class, 'store']);
$router->post('/admin/repository/{id}/update', [AdminRepository::class, 'update']);
$router->post('/admin/repository/{id}/delete', [AdminRepository::class, 'delete']);

$router->get('/admin/mata-kuliah', [AdminMataKuliah::class, 'index']);
$router->post('/admin/mata-kuliah', [AdminMataKuliah::class, 'store']);
$router->post('/admin/mata-kuliah/{id}/update', [AdminMataKuliah::class, 'update']);
$router->post('/admin/mata-kuliah/{id}/delete', [AdminMataKuliah::class, 'delete']);

$router->get('/admin/users', [AdminUser::class, 'index']);
$router->post('/admin/users', [AdminUser::class, 'store']);
$router->post('/admin/users/{id}/update', [AdminUser::class, 'update']);
$router->post('/admin/users/{id}/toggle-ban', [AdminUser::class, 'toggleBan']);
$router->get('/admin/users/export', [AdminUser::class, 'export']);
$router->post('/admin/users/import', [AdminUser::class, 'import']);
$router->post('/admin/users/{id}/delete', [AdminUser::class, 'delete']);

$router->get('/admin/profile', [AdminProfile::class, 'index']);
$router->post('/admin/profile', [AdminProfile::class, 'update']);
$router->get('/admin/settings', [AdminSetting::class, 'index']);
$router->post('/admin/settings/maintenance', [AdminSetting::class, 'maintenance']);
$router->get('/admin/settings/backup', [AdminSetting::class, 'backup']);
$router->get('/admin/logs', [AdminLog::class, 'index']);
$router->post('/admin/logs/reset-old', [AdminLog::class, 'resetOld']);


/* ============================================================
   DASHBOARD DEVELOPER
============================================================ */
$router->get('/developer', static function () use ($redirectTo): void {
    $redirectTo('/developer/dashboard');
});
$router->get('/__developer', static function () use ($redirectTo): void {
    $redirectTo('/developer/dashboard');
});
$router->get('/__developer/dashboard', static function () use ($redirectTo): void {
    $redirectTo('/developer/dashboard');
});
$router->get('/developer/dashboard', [DeveloperDashboard::class, 'index']);
$router->get('/developer/profile', [DeveloperProfile::class, 'index']);
$router->post('/developer/profile', [DeveloperProfile::class, 'update']);
$router->get('/developer/users', [AdminUser::class, 'index']);
$router->post('/developer/users', [AdminUser::class, 'store']);
$router->post('/developer/users/{id}/update', [AdminUser::class, 'update']);
$router->post('/developer/users/{id}/toggle-ban', [AdminUser::class, 'toggleBan']);
$router->get('/developer/users/export', [AdminUser::class, 'export']);
$router->post('/developer/users/import', [AdminUser::class, 'import']);
$router->post('/developer/users/{id}/delete', [AdminUser::class, 'delete']);
$router->get('/developer/settings', [AdminSetting::class, 'index']);
$router->post('/developer/settings/maintenance', [AdminSetting::class, 'maintenance']);
$router->get('/developer/settings/backup', [AdminSetting::class, 'backup']);


/* ============================================================
   API ENDPOINTS (JSON)
============================================================ */
$router->post('/api/login', [AuthApiController::class, 'login']);
$router->post('/api/mobile/login/mahasiswa', [AuthApiController::class, 'loginMahasiswa']);
$router->post('/api/mobile/login/dosen', [AuthApiController::class, 'loginDosen']);
$router->post('/api/logout', [AuthApiController::class, 'logout']);
$router->get('/api/me', [AuthApiController::class, 'me']);
$router->get('/api/profile', [ProfileApiController::class, 'show']);
$router->post('/api/profile', [ProfileApiController::class, 'update']);
$router->put('/api/profile', [ProfileApiController::class, 'update']);
$router->patch('/api/profile', [ProfileApiController::class, 'update']);

$router->post('/api/forgot-password/request', [ForgotPasswordApiController::class, 'requestReset']);
$router->get('/api/forgot-password/verify/{token}', [ForgotPasswordApiController::class, 'verifyToken']);
$router->post('/api/forgot-password/reset', [ForgotPasswordApiController::class, 'resetPassword']);

$router->get('/api/repositories', [RepositoryApiController::class, 'publicIndex']);
$router->get('/api/repositories/{slug}', [RepositoryApiController::class, 'publicShow']);
$router->get('/api/repositories/{id}/download', [RepositoryApiController::class, 'download']);

$router->get('/api/lookups', [LookupApiController::class, 'index']);
$router->get('/api/lookups/categories', [LookupApiController::class, 'categories']);
$router->get('/api/categories', [LookupApiController::class, 'categories']);
$router->get('/api/categories/page', [LookupApiController::class, 'categoryPage']);
$router->get('/api/categories/{type}/{value}/repositories', [RepositoryApiController::class, 'publicByCategory']);

$router->get('/api/bookmarks', [BookmarkApiController::class, 'index']);
$router->post('/api/bookmarks', [BookmarkApiController::class, 'store']);
$router->get('/api/bookmarks/{id}', [BookmarkApiController::class, 'show']);
$router->delete('/api/bookmarks/{id}', [BookmarkApiController::class, 'destroy']);
$router->post('/api/bookmarks/toggle', [BookmarkApiController::class, 'toggle']);

// Mobile public
$router->get('/api/mobile/public/home', [MobileAdminApiController::class, 'publicHome']);
$router->get('/api/mobile/public/lookups', [LookupApiController::class, 'index']);
$router->get('/api/mobile/public/categories', [LookupApiController::class, 'categories']);
$router->get('/api/mobile/public/categories/page', [LookupApiController::class, 'categoryPage']);
$router->get('/api/mobile/public/categories/{type}/{value}/repositories', [RepositoryApiController::class, 'publicByCategory']);
$router->get('/api/mobile/public/repositories', [RepositoryApiController::class, 'publicIndex']);
$router->get('/api/mobile/public/repositories/{slug}', [RepositoryApiController::class, 'publicShow']);
$router->get('/api/mobile/public/repositories/{id}/download', [RepositoryApiController::class, 'download']);
$router->post('/api/mobile/forgot-password/request', [ForgotPasswordApiController::class, 'requestReset']);
$router->get('/api/mobile/forgot-password/verify/{token}', [ForgotPasswordApiController::class, 'verifyToken']);
$router->post('/api/mobile/forgot-password/reset', [ForgotPasswordApiController::class, 'resetPassword']);

// Mobile mahasiswa
$router->get('/api/mobile/mahasiswa/dashboard', [MobileAdminApiController::class, 'mahasiswaDashboard']);
$router->get('/api/mobile/mahasiswa/repositories', [MobileAdminApiController::class, 'mahasiswaRepositories']);
$router->get('/api/mobile/mahasiswa/bookmarks', [MobileAdminApiController::class, 'bookmarks']);
$router->post('/api/mobile/mahasiswa/bookmarks', [BookmarkApiController::class, 'store']);
$router->get('/api/mobile/mahasiswa/bookmarks/{id}', [BookmarkApiController::class, 'show']);
$router->delete('/api/mobile/mahasiswa/bookmarks/{id}', [BookmarkApiController::class, 'destroy']);
$router->post('/api/mobile/mahasiswa/bookmarks/toggle', [MobileAdminApiController::class, 'toggleBookmark']);
$router->get('/api/mobile/mahasiswa/activities', [MobileAdminApiController::class, 'activities']);
$router->get('/api/mobile/mahasiswa/profile', [ProfileApiController::class, 'showMahasiswa']);
$router->post('/api/mobile/mahasiswa/profile', [ProfileApiController::class, 'updateMahasiswa']);
$router->put('/api/mobile/mahasiswa/profile', [ProfileApiController::class, 'updateMahasiswa']);
$router->patch('/api/mobile/mahasiswa/profile', [ProfileApiController::class, 'updateMahasiswa']);

// Mobile dosen
$router->get('/api/mobile/dosen/dashboard', [MobileAdminApiController::class, 'dosenDashboard']);
$router->get('/api/mobile/dosen/bookmarks', [MobileAdminApiController::class, 'bookmarks']);
$router->post('/api/mobile/dosen/bookmarks', [BookmarkApiController::class, 'store']);
$router->get('/api/mobile/dosen/bookmarks/{id}', [BookmarkApiController::class, 'show']);
$router->delete('/api/mobile/dosen/bookmarks/{id}', [BookmarkApiController::class, 'destroy']);
$router->post('/api/mobile/dosen/bookmarks/toggle', [MobileAdminApiController::class, 'toggleBookmark']);
$router->get('/api/mobile/dosen/activities', [MobileAdminApiController::class, 'activities']);
$router->get('/api/mobile/dosen/repositories', [RepositoryApiController::class, 'dosenIndex']);
$router->post('/api/mobile/dosen/repositories', [RepositoryApiController::class, 'store']);
$router->get('/api/mobile/dosen/repositories/{id}', [RepositoryApiController::class, 'show']);
$router->put('/api/mobile/dosen/repositories/{id}', [RepositoryApiController::class, 'update']);
$router->patch('/api/mobile/dosen/repositories/{id}', [RepositoryApiController::class, 'update']);
$router->delete('/api/mobile/dosen/repositories/{id}', [RepositoryApiController::class, 'destroy']);
$router->post('/api/mobile/dosen/repositories/{id}/upload', [RepositoryApiController::class, 'uploadFile']);
$router->get('/api/mobile/dosen/profile', [ProfileApiController::class, 'showDosen']);
$router->post('/api/mobile/dosen/profile', [ProfileApiController::class, 'updateDosen']);
$router->put('/api/mobile/dosen/profile', [ProfileApiController::class, 'updateDosen']);
$router->patch('/api/mobile/dosen/profile', [ProfileApiController::class, 'updateDosen']);

/* ============================================================
   MAINTENANCE
============================================================ */
$router->get('/maintenance',        [MaintenanceController::class, 'index']);

/* return router */
return $router;
