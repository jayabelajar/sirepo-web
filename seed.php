<?php
/**
 * Seeder sederhana untuk membuat akun admin, dosen, dan mahasiswa.
 * Jalankan dari root project: php seed.php
 */

// === Muat ENV agar koneksi mengikuti konfigurasi aplikasi ===
$envFile = __DIR__ . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $k = trim($k);
        $v = trim($v);
        if ($v !== '' && ($v[0] === '"' || $v[0] === "'")) {
            $v = trim($v, '\'"');
        }
        putenv("{$k}={$v}");
        $_ENV[$k] = $v;
    }
}

$dbHost    = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort    = (int) (getenv('DB_PORT') ?: 3306);
$dbName    = getenv('DB_NAME') ?: 'sirepov1';
$dbUser    = getenv('DB_USER') ?: 'root';
$dbPass    = getenv('DB_PASS') ?: '';
$dbCharset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbHost, $dbPort, $dbName, $dbCharset);

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    fwrite(STDERR, "Koneksi DB gagal: {$e->getMessage()}" . PHP_EOL);
    exit(1);
}

$accounts = [
    [
        'username'      => 'admin1',
        'nama_lengkap'  => 'Admin Utama',
        'email'         => 'admin@gmail.com',
        'password'      => 'admin12345',
        'role'          => 'admin',
        'nim'           => null,
        'nidn_nip'      => null,
    ],
    [
        'username'      => 'dosen1',
        'nama_lengkap'  => 'Dosen Penguji',
        'email'         => 'dosen1@gmail.com',
        'password'      => 'dosen12345',
        'role'          => 'dosen',
        'nim'           => null,
        'nidn_nip'      => '1212121212',
    ],
    [
        'username'      => 'mhs1',
        'nama_lengkap'  => 'Mahasiswa Aktif',
        'email'         => 'mhs1@gmail.com',
        'password'      => 'mhs12345',
        'role'          => 'mahasiswa',
        'nim'           => 'E41241021',
        'nidn_nip'      => null,
    ],
];

$checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
$insertSql = <<<SQL
INSERT INTO users (username, nama_lengkap, email, password_hash, role, nim, nidn_nip)
VALUES (:username, :nama_lengkap, :email, :password_hash, :role, :nim, :nidn_nip)
SQL;
$insertStmt = $pdo->prepare($insertSql);

$inserted = 0;
$skipped  = 0;

foreach ($accounts as $account) {
    $checkStmt->execute([
        ':email'    => $account['email'],
        ':username' => $account['username'],
    ]);
    $existingId = $checkStmt->fetchColumn();

    if ($existingId) {
        echo "[SKIP] {$account['email']} sudah ada (id={$existingId})" . PHP_EOL;
        $skipped++;
        continue;
    }

    $insertStmt->execute([
        ':username'      => $account['username'],
        ':nama_lengkap'  => $account['nama_lengkap'],
        ':email'         => $account['email'],
        ':password_hash' => password_hash($account['password'], PASSWORD_BCRYPT),
        ':role'          => $account['role'],
        ':nim'           => $account['nim'],
        ':nidn_nip'      => $account['nidn_nip'],
    ]);

    echo "[OK] Tambah {$account['role']} => {$account['email']}" . PHP_EOL;
    $inserted++;
}

echo PHP_EOL . "Seeder selesai. Inserted={$inserted}, Skipped={$skipped}" . PHP_EOL;
