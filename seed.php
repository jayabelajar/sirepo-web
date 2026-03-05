<?php
// seed_users.php
// Jalankan: php seed_users.php

$host = "127.0.0.1";
$port = 3306;
$db   = "sirepov1";
$user = "root";
$pass = ""; // sesuaikan

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  die("Koneksi DB gagal: " . $e->getMessage() . PHP_EOL);
}

// Data seed 3 role
$rows = [
  [
    "username" => "admin1",
    "nama_lengkap" => "Admin 1",
    "email" => "admin@gmail.com",
    "password_plain" => "admin12345",
    "role" => "admin",
    "nim" => null,
    "nidn_nip" => null,
  ],
  [
    "username" => "dosen1",
    "nama_lengkap" => "Dosen 1",
    "email" => "dosen1@gmail.com",
    "password_plain" => "dosen12345",
    "role" => "dosen",
    "nim" => null,
    "nidn_nip" => "1212121212",
  ],
  [
    "username" => "mhs1",
    "nama_lengkap" => "Mahasiswa 1",
    "email" => "mhs1@gmail.com",
    "password_plain" => "mhs12345",
    "role" => "mahasiswa",
    "nim" => "E41241021",
    "nidn_nip" => null,
  ],
];

// Insert (skip kalau email sudah ada)
$sql = "INSERT INTO users (username, nama_lengkap, email, password_hash, role, nim, nidn_nip)
        VALUES (:username, :nama_lengkap, :email, :password_hash, :role, :nim, :nidn_nip)";

$check = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$ins   = $pdo->prepare($sql);

$inserted = 0;
$skipped  = 0;

foreach ($rows as $r) {
  $check->execute([":email" => $r["email"]]);
  $exist = $check->fetchColumn();

  if ($exist) {
    echo "SKIP: {$r['email']} (sudah ada, id={$exist})" . PHP_EOL;
    $skipped++;
    continue;
  }

  $hash = password_hash($r["password_plain"], PASSWORD_BCRYPT);

  $ins->execute([
    ":username"      => $r["username"],
    ":nama_lengkap"  => $r["nama_lengkap"],
    ":email"         => $r["email"],
    ":password_hash" => $hash,
    ":role"          => $r["role"],
    ":nim"           => $r["nim"],
    ":nidn_nip"      => $r["nidn_nip"],
  ]);

  echo "OK: insert {$r['role']} -> {$r['email']}" . PHP_EOL;
  $inserted++;
}

echo PHP_EOL . "Selesai. Inserted={$inserted}, Skipped={$skipped}" . PHP_EOL;