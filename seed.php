<?php
/**
 * Seeder sederhana untuk membuat akun admin, dosen, mahasiswa dan data pendukung repositori.
 * Jalankan dari root project: php seed.php
 */

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    return trim($text, '-') ?: 'judul-' . uniqid();
}

function normalizeJenisKarya(string $value): string
{
    $v = strtolower(trim($value));
    $v = str_replace(['-', ' '], '_', $v);

    $map = [
        'skripsi'        => 'skripsi',
        'tugas_akhir'    => 'tugas_akhir',
        'jurnal'         => 'jurnal',
        'artikel'        => 'artikel',
        'artikel_ilmiah' => 'artikel',
        'laporan'        => 'laporan',
        'pkl'            => 'pkl',
        'tesis'          => 'lainnya',
        'thesis'         => 'lainnya',
        'lainnya'        => 'lainnya',
    ];

    return $map[$v] ?? 'lainnya';
}

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

// Pastikan enum role mendukung developer.
try {
    $roleColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch();
    $roleType = strtolower((string) ($roleColumn['Type'] ?? ''));
    if (strpos($roleType, "enum(") === 0 && strpos($roleType, "'developer'") === false) {
        $pdo->exec("ALTER TABLE users MODIFY role ENUM('admin','developer','dosen','mahasiswa') NOT NULL");
        echo "[OK] Enum role users diperbarui untuk mendukung developer" . PHP_EOL;
    }
} catch (Throwable $e) {
    fwrite(STDERR, "[WARN] Gagal memastikan enum role developer: {$e->getMessage()}" . PHP_EOL);
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
        'username'      => 'admin2',
        'nama_lengkap'  => 'Admin Sekunder',
        'email'         => 'admin2@gmail.com',
        'password'      => 'admin12345',
        'role'          => 'admin',
        'nim'           => null,
        'nidn_nip'      => null,
    ],
    [
        'username'      => 'developer1',
        'nama_lengkap'  => 'Developer Sistem',
        'email'         => 'developer@gmail.com',
        'password'      => 'developer12345',
        'role'          => 'developer',
        'nim'           => null,
        'nidn_nip'      => null,
    ],
    [
        'username'      => 'dosen1',
        'nama_lengkap'  => 'Dosen Penguji 1',
        'email'         => 'dosen1@gmail.com',
        'password'      => 'dosen12345',
        'role'          => 'dosen',
        'nim'           => null,
        'nidn_nip'      => '1212121212',
    ],
    [
        'username'      => 'dosen2',
        'nama_lengkap'  => 'Dosen Penguji 2',
        'email'         => 'dosen2@gmail.com',
        'password'      => 'dosen12345',
        'role'          => 'dosen',
        'nim'           => null,
        'nidn_nip'      => '2323232323',
    ],
    [
        'username'      => 'mhs1',
        'nama_lengkap'  => 'Mahasiswa Aktif 1',
        'email'         => 'mhs1@gmail.com',
        'password'      => 'mhs12345',
        'role'          => 'mahasiswa',
        'nim'           => 'E41241021',
        'nidn_nip'      => null,
    ],
    [
        'username'      => 'mhs2',
        'nama_lengkap'  => 'Mahasiswa Aktif 2',
        'email'         => 'mhs2@gmail.com',
        'password'      => 'mhs12345',
        'role'          => 'mahasiswa',
        'nim'           => 'E41241022',
        'nidn_nip'      => null,
    ],
    [
        'username'      => 'mhs3',
        'nama_lengkap'  => 'Mahasiswa Aktif 3',
        'email'         => 'mhs3@gmail.com',
        'password'      => 'mhs12345',
        'role'          => 'mahasiswa',
        'nim'           => 'E41241023',
        'nidn_nip'      => null,
    ],
    [
        'username'      => 'mhs4',
        'nama_lengkap'  => 'Mahasiswa Aktif 4',
        'email'         => 'mhs4@gmail.com',
        'password'      => 'mhs12345',
        'role'          => 'mahasiswa',
        'nim'           => 'E41241024',
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
echo str_repeat('-', 60) . PHP_EOL;

// === Seed Program Studi ===
$studyPrograms = [
    'Teknik Informatika',
    'Pendidikan Agama Islam',
    'Ekonomi Syariah',
    'Hukum Keluarga Islam',
];

$programStmt = $pdo->prepare('SELECT id FROM program_studi WHERE nama_program_studi = :nama LIMIT 1');
$programInsert = $pdo->prepare('INSERT INTO program_studi (nama_program_studi) VALUES (:nama)');
$programIds = [];
$psInserted = 0;

foreach ($studyPrograms as $program) {
    $programStmt->execute([':nama' => $program]);
    $id = $programStmt->fetchColumn();
    if (!$id) {
        $programInsert->execute([':nama' => $program]);
        $id = $pdo->lastInsertId();
        $psInserted++;
        echo "[OK] Tambah Program Studi {$program}" . PHP_EOL;
    } else {
        echo "[SKIP] Program Studi {$program}" . PHP_EOL;
    }
    $programIds[$program] = (int) $id;
}

echo "Program Studi: Inserted={$psInserted}, Total=" . count($programIds) . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

// === Seed Mata Kuliah ===
$subjectMap = [
    'Teknik Informatika' => [
        'Kecerdasan Buatan',
        'Basis Data Lanjut',
        'Pengembangan Web',
    ],
    'Pendidikan Agama Islam' => [
        'Metodologi Pendidikan Islam',
        'Psikologi Pendidikan',
    ],
    'Ekonomi Syariah' => [
        'Manajemen Zakat dan Wakaf',
        'Akuntansi Syariah',
    ],
    'Hukum Keluarga Islam' => [
        'Fiqh Munakahat',
        'Hukum Waris Islam',
    ],
];

$subjectStmt = $pdo->prepare('SELECT id FROM mata_kuliah WHERE nama = :nama LIMIT 1');
$subjectInsert = $pdo->prepare('INSERT INTO mata_kuliah (nama) VALUES (:nama)');
$subjectIds = [];
$mkInserted = 0;

foreach ($subjectMap as $subjects) {
    foreach ($subjects as $subject) {
        if (isset($subjectIds[$subject])) continue;
        $subjectStmt->execute([':nama' => $subject]);
        $id = $subjectStmt->fetchColumn();
        if (!$id) {
            $subjectInsert->execute([':nama' => $subject]);
            $id = $pdo->lastInsertId();
            $mkInserted++;
            echo "[OK] Tambah Mata Kuliah {$subject}" . PHP_EOL;
        } else {
            echo "[SKIP] Mata Kuliah {$subject}" . PHP_EOL;
        }
        $subjectIds[$subject] = (int) $id;
    }
}

echo "Mata Kuliah: Inserted={$mkInserted}, Total=" . count($subjectIds) . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

// === Seed Repositori Contoh ===
$userStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$uploadedBy = [
    'admin' => null,
    'dosen' => null,
];
foreach (['admin@gmail.com' => 'admin', 'dosen1@gmail.com' => 'dosen'] as $email => $key) {
    $userStmt->execute([':email' => $email]);
    $uploadedBy[$key] = (int) ($userStmt->fetchColumn() ?: 0);
}

$repoSamples = [
    [
        'judul'        => 'Penerapan Machine Learning untuk Klasifikasi Hadis',
        'jenis_karya'  => 'skripsi',
        'author'       => 'Ahmad Fauzi',
        'tahun'        => 2024,
        'program'      => 'Teknik Informatika',
        'mata_kuliah'  => 'Kecerdasan Buatan',
        'abstrak'      => 'Penelitian ini membahas pemanfaatan machine learning untuk mengklasifikasikan hadis berdasarkan tema dan sumber.',
        'keywords'     => ['machine learning', 'hadis', 'klasifikasi'],
        'file'         => 'files/sample-ml-hadis.pdf',
        'uploader'     => $uploadedBy['dosen'] ?: $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Strategi Pembelajaran Akhlak untuk Gen-Z di Pesantren',
        'jenis_karya'  => 'artikel',
        'author'       => 'Nur Aini',
        'tahun'        => 2023,
        'program'      => 'Pendidikan Agama Islam',
        'mata_kuliah'  => 'Metodologi Pendidikan Islam',
        'abstrak'      => 'Artikel ini merangkum pendekatan pembelajaran akhlak bagi santri Generasi Z dengan memadukan metode tradisional dan digital.',
        'keywords'     => ['pendidikan', 'akhlak', 'gen-z'],
        'file'         => 'files/strategi-pembelajaran.pdf',
        'uploader'     => $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Optimalisasi Manajemen Wakaf Produktif untuk Pemberdayaan Umat',
        'jenis_karya'  => 'lainnya',
        'author'       => 'Rahmat Hidayat',
        'tahun'        => 2022,
        'program'      => 'Ekonomi Syariah',
        'mata_kuliah'  => 'Manajemen Zakat dan Wakaf',
        'abstrak'      => 'Kajian ini meneliti model pengelolaan wakaf produktif guna meningkatkan kemandirian ekonomi masyarakat.',
        'keywords'     => ['wakaf', 'ekonomi syariah', 'pemberdayaan'],
        'file'         => 'files/wakaf-produktif.pdf',
        'uploader'     => $uploadedBy['dosen'] ?: $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Analisis Sentimen Twitter terhadap Kebijakan Ekonomi Syariah',
        'jenis_karya'  => 'skripsi',
        'author'       => 'Siti Aminah',
        'tahun'        => 2024,
        'program'      => 'Teknik Informatika',
        'mata_kuliah'  => 'Basis Data Lanjut',
        'abstrak'      => 'Menganalisis respon publik di media sosial Twitter terhadap regulasi terbaru mengenai perbankan syariah di Indonesia.',
        'keywords'     => ['sentimen', 'twitter', 'ekonomi syariah'],
        'file'         => 'files/sentimen-ekonomi.pdf',
        'uploader'     => $uploadedBy['dosen'] ?: $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Sistem Informasi Manajemen Inventaris Masjid Berbasis Web',
        'jenis_karya'  => 'tugas_akhir',
        'author'       => 'Budi Santoso',
        'tahun'        => 2023,
        'program'      => 'Teknik Informatika',
        'mata_kuliah'  => 'Pengembangan Web',
        'abstrak'      => 'Pengembangan aplikasi web untuk membantu pengurus masjid dalam mengelola data aset dan inventaris secara transparan.',
        'keywords'     => ['sistem informasi', 'web', 'masjid'],
        'file'         => 'files/sim-masjid.pdf',
        'uploader'     => $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Tinjauan Hukum Islam terhadap Praktik Jual Beli Mystery Box',
        'jenis_karya'  => 'jurnal',
        'author'       => 'Zubair Ali',
        'tahun'        => 2024,
        'program'      => 'Hukum Keluarga Islam',
        'mata_kuliah'  => 'Fiqh Munakahat',
        'abstrak'      => 'Penelitian hukum mengenai keabsahan transaksi mystery box dalam perspektif fiqh muamalah, khususnya unsur gharar.',
        'keywords'     => ['hukum islam', 'mystery box', 'gharar'],
        'file'         => 'files/hukum-mystery-box.pdf',
        'uploader'     => $uploadedBy['dosen'] ?: $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Pengaruh Psikologi Islam terhadap Ketenangan Jiwa Remaja',
        'jenis_karya'  => 'skripsi',
        'author'       => 'Laila Majnun',
        'tahun'        => 2023,
        'program'      => 'Pendidikan Agama Islam',
        'mata_kuliah'  => 'Psikologi Pendidikan',
        'abstrak'      => 'Studi mengenai korelasi antara pemahaman nilai-nilai spiritual Islam dengan tingkat kecemasan pada remaja di perkotaan.',
        'keywords'     => ['psikologi', 'islam', 'remaja'],
        'file'         => 'files/psikologi-remaja.pdf',
        'uploader'     => $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Implementasi Blockchain pada Sistem Pemungutan Suara Elektronik',
        'jenis_karya'  => 'skripsi',
        'author'       => 'Dedi Kurniawan',
        'tahun'        => 2024,
        'program'      => 'Teknik Informatika',
        'mata_kuliah'  => 'Pengembangan Web',
        'abstrak'      => 'Rancang bangun sistem e-voting yang aman dan transparan menggunakan teknologi blockchain untuk organisasi kemahasiswaan.',
        'keywords'     => ['blockchain', 'e-voting', 'keamanan'],
        'file'         => 'files/blockchain-voting.pdf',
        'uploader'     => $uploadedBy['dosen'] ?: $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Analisis Akuntansi Syariah pada Lembaga Amil Zakat Lokal',
        'jenis_karya'  => 'laporan',
        'author'       => 'Fatimah Az-Zahra',
        'tahun'        => 2022,
        'program'      => 'Ekonomi Syariah',
        'mata_kuliah'  => 'Akuntansi Syariah',
        'abstrak'      => 'Laporan observasi mengenai penerapan standar akuntansi zakat (PSAK 109) pada beberapa LAZ di tingkat kabupaten.',
        'keywords'     => ['akuntansi', 'zakat', 'laz'],
        'file'         => 'files/akuntansi-zakat.pdf',
        'uploader'     => $uploadedBy['admin'],
    ],
    [
        'judul'        => 'Peran Mediasi dalam Penyelesaian Sengketa Waris di Pengadilan Agama',
        'jenis_karya'  => 'artikel',
        'author'       => 'Hasan Basri',
        'tahun'        => 2023,
        'program'      => 'Hukum Keluarga Islam',
        'mata_kuliah'  => 'Hukum Waris Islam',
        'abstrak'      => 'Mengkaji efektivitas proses mediasi dalam meredam konflik keluarga pada sengketa pembagian harta warisan.',
        'keywords'     => ['mediasi', 'waris', 'hukum'],
        'file'         => 'files/mediasi-waris.pdf',
        'uploader'     => $uploadedBy['dosen'] ?: $uploadedBy['admin'],
    ],
];

$repoCheck = $pdo->prepare('SELECT id FROM repository WHERE slug = :slug LIMIT 1');
$repoInsert = $pdo->prepare("
    INSERT INTO repository
        (judul, slug, jenis_karya, author, tahun, program_studi_id, mata_kuliah_id, abstrak, file_pdf, uploaded_by, keywords)
    VALUES
        (:judul, :slug, :jenis_karya, :author, :tahun, :program_studi_id, :mata_kuliah_id, :abstrak, :file_pdf, :uploaded_by, :keywords)
");

$repoInserted = 0;
foreach ($repoSamples as $repo) {
    $slug = slugify($repo['judul']);
    $repoCheck->execute([':slug' => $slug]);
    if ($repoCheck->fetchColumn()) {
        echo "[SKIP] Repository {$repo['judul']}" . PHP_EOL;
        continue;
    }

    $psId = $programIds[$repo['program']] ?? null;
    $mkId = $subjectIds[$repo['mata_kuliah']] ?? null;

    $repoInsert->execute([
        ':judul'            => $repo['judul'],
        ':slug'             => $slug,
        ':jenis_karya'      => normalizeJenisKarya((string) $repo['jenis_karya']),
        ':author'           => $repo['author'],
        ':tahun'            => $repo['tahun'],
        ':program_studi_id' => $psId,
        ':mata_kuliah_id'   => $mkId,
        ':abstrak'          => $repo['abstrak'],
        ':file_pdf'         => $repo['file'],
        ':uploaded_by'      => $repo['uploader'] ?: $uploadedBy['admin'],
        ':keywords'         => implode(',', $repo['keywords']),
    ]);

    echo "[OK] Tambah Repository {$repo['judul']}" . PHP_EOL;
    $repoInserted++;
}

echo "Repository Contoh: Inserted={$repoInserted}" . PHP_EOL;
