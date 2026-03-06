<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security\Auth;
use App\Core\Security\Csrf;
use App\Core\Security\Sanitizer;
use App\Models\User;
use App\Core\Database;
use App\Core\Helpers\ActivityLogger;

class UserController extends Controller
{
    private function panelPrefix(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        return strpos($path, '/developer') === 0 ? '/developer' : '/admin';
    }

    private function usersPath(): string
    {
        return $this->panelPrefix() . '/users';
    }

    private function headerActivities(): array
    {
        $db = Database::getConnection();
        $rows = $db->query("
            SELECT al.description, al.activity_type, al.created_at, u.nama_lengkap
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.created_at DESC
            LIMIT 8
        ")->fetchAll() ?: [];

        return array_map(static function ($row) {
            return [
                'actor'  => $row['nama_lengkap'] ?: 'System',
                'action' => $row['description'] ?: $row['activity_type'],
                'time'   => $row['created_at'],
            ];
        }, $rows);
    }

    public function index()
    {
        $account = Auth::checkAdmin();
        $panelPrefix = $this->panelPrefix();
        $userModel = new User();
        $q = trim($_GET['q'] ?? '');
        $role = $_GET['role'] ?? null;
        if (!in_array($role, ['admin','developer','dosen','mahasiswa'], true)) {
            $role = null;
        }
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $items   = $userModel->getAllOrdered($q, $role, $perPage, $offset);
        $total   = $userModel->countAll($q, $role);
        $pages   = (int) ceil($total / $perPage);

        return $this->view('admin/user', [
            'admin'      => $account,
            'actor_role' => $account['role'] ?? 'admin',
            'items'      => $items,
            'search'     => $q,
            'filter_role'=> $role,
            'csrf'       => Csrf::token(),
            'header_activities' => $this->headerActivities(),
            'panel_prefix' => $panelPrefix,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => $panelPrefix . '/dashboard'],
                ['label' => 'Pengguna'],
            ],
            'page'       => $page,
            'pages'      => $pages,
            'per_page'   => $perPage,
            'total'      => $total,
        ], 'admin');
    }

    public function store()
    {
        $account = Auth::checkAdmin();
        $usersPath = $this->usersPath();
        $saveAndNew = !empty($_POST['save_and_new']);
        Sanitizer::cleanRequest();
        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect($saveAndNew ? $usersPath . '?open=create' : $usersPath);
        }

        $data = $this->collectData();
        if ($data['password_hash'] === null) {
            $_SESSION['flash_error'] = 'Password wajib diisi.';
            return $this->redirect($saveAndNew ? $usersPath . '?open=create' : $usersPath);
        }
        if (($account['role'] ?? '') === 'admin' && ($data['role'] ?? '') === 'developer') {
            $_SESSION['flash_error'] = 'Admin tidak dapat menambahkan user dengan role developer.';
            return $this->redirect($saveAndNew ? $usersPath . '?open=create' : $usersPath);
        }

        $userModel = new User();
        $userModel->create($data);
        ActivityLogger::log($account['id'] ?? null, 'create_user', 'users', null, 'Menambah pengguna: ' . ($data['email'] ?? ''));
        $_SESSION['flash_success'] = 'Pengguna ditambahkan.';
        return $this->redirect($saveAndNew ? $usersPath . '?open=create' : $usersPath);
    }

    public function update($id)
    {
        $account = Auth::checkAdmin();
        $usersPath = $this->usersPath();
        Sanitizer::cleanRequest();
        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect($usersPath);
        }

        $data = $this->collectData(false);
        $userModel = new User();
        $targetUser = $userModel->findById((int) $id);
        if (($targetUser['role'] ?? '') === 'developer') {
            if (($account['role'] ?? '') === 'admin') {
                $_SESSION['flash_error'] = 'Akun developer tidak dapat diedit oleh admin.';
                return $this->redirect($usersPath);
            }
            // Hindari demosi role developer agar tidak bisa diakali untuk proses hapus.
            $data['role'] = 'developer';
        }
        if (($account['role'] ?? '') === 'admin' && ($data['role'] ?? '') === 'developer') {
            $_SESSION['flash_error'] = 'Admin tidak dapat menetapkan role developer.';
            return $this->redirect($usersPath);
        }
        $userModel->updateUser((int)$id, $data);
        ActivityLogger::log($account['id'] ?? null, 'update_user', 'users', (int)$id, 'Memperbarui pengguna: ' . ($data['email'] ?? ''));
        $_SESSION['flash_success'] = 'Pengguna diperbarui.';
        return $this->redirect($usersPath);
    }

    public function toggleBan($id)
    {
        $account = Auth::checkAdmin();
        $usersPath = $this->usersPath();
        Sanitizer::cleanRequest();
        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect($usersPath);
        }

        $userModel = new User();
        $user = $userModel->findById((int) $id);
        if (!$user) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            return $this->redirect($usersPath);
        }

        if (!in_array($user['role'], ['admin','developer','dosen'], true)) {
            $_SESSION['flash_error'] = 'Aksi ini hanya untuk akun admin/developer/dosen.';
            return $this->redirect($usersPath);
        }
        if (($account['role'] ?? '') === 'admin' && ($user['role'] ?? '') === 'developer') {
            $_SESSION['flash_error'] = 'Akun developer tidak dapat dibekukan oleh admin.';
            return $this->redirect($usersPath);
        }

        $now = time();
        $isBanned = !empty($user['banned_until']) && strtotime($user['banned_until']) > $now;
        if ($isBanned) {
            $userModel->resetLoginAttempts((int) $id);
            $userModel->setBanUntil((int) $id, null);
            $_SESSION['flash_success'] = 'Pengguna berhasil di-unban.';
            ActivityLogger::log($account['id'] ?? null, 'unban_user', 'users', (int)$id, 'Membuka blokir pengguna: ' . ($user['email'] ?? ''));
        } else {
            $banUntil = date('Y-m-d H:i:s', $now + 7 * 24 * 60 * 60);
            $userModel->setBanUntil((int) $id, $banUntil);
            $_SESSION['flash_success'] = 'Pengguna diblokir sampai ' . $banUntil . '.';
            ActivityLogger::log($account['id'] ?? null, 'ban_user', 'users', (int)$id, 'Memblokir pengguna: ' . ($user['email'] ?? ''));
        }

        return $this->redirect($usersPath);
    }

    public function export()
    {
        Auth::checkAdmin();
        $userModel = new User();
        $items = $userModel->getAllOrdered();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="users.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['nama_lengkap','email','username','role','nim','nidn_nip','created_at']);
        foreach ($items as $u) {
            fputcsv($out, [
                $u['nama_lengkap'] ?? '',
                $u['email'] ?? '',
                $u['username'] ?? '',
                $u['role'] ?? '',
                $u['nim'] ?? '',
                $u['nidn_nip'] ?? '',
                $u['created_at'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }

    public function import()
    {
        $account = Auth::checkAdmin();
        $usersPath = $this->usersPath();
        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect($usersPath);
        }
        if (empty($_FILES['csv_file']) || ($_FILES['csv_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'File CSV tidak ditemukan.';
            return $this->redirect($usersPath);
        }

        $tmp = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if (!$handle) {
            $_SESSION['flash_error'] = 'Gagal membuka file CSV.';
            return $this->redirect($usersPath);
        }

        $userModel = new User();
        $row = 0;
        $imported = 0;
        $blockedDeveloper = 0;
        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if ($row === 1 && isset($data[0]) && stripos($data[0], 'nama') !== false) {
                continue; // skip header
            }
            [$nama, $email, $username, $role, $nim, $nidn, $password] = array_pad($data, 7, '');
            $role = in_array($role, ['admin','developer','dosen','mahasiswa'], true) ? $role : 'mahasiswa';
            if (($account['role'] ?? '') === 'admin' && $role === 'developer') {
                $blockedDeveloper++;
                continue;
            }
            if ($email === '' || $password === '') {
                continue;
            }
            $userModel->create([
                'nama_lengkap'  => trim($nama),
                'email'         => trim($email),
                'username'      => trim($username) ?: null,
                'role'          => $role,
                'nim'           => $role === 'mahasiswa' ? trim($nim) : null,
                'nidn_nip'      => $role === 'dosen' ? trim($nidn) : null,
                'password_hash' => password_hash(trim($password), PASSWORD_BCRYPT),
            ]);
            $imported++;
        }
        fclose($handle);

        ActivityLogger::log($account['id'] ?? null, 'import_user', 'users', null, "Import pengguna berhasil: {$imported} baris");
        $_SESSION['flash_success'] = "Import selesai. Berhasil: {$imported} baris."
            . ($blockedDeveloper > 0 ? " Ditolak role developer: {$blockedDeveloper} baris." : '');
        return $this->redirect($usersPath);
    }

    public function delete($id)
    {
        $account = Auth::checkAdmin();
        $usersPath = $this->usersPath();
        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect($usersPath);
        }

        $userModel = new User();
        $targetUser = $userModel->findById((int) $id);
        if (!$targetUser) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            return $this->redirect($usersPath);
        }
        if (($account['role'] ?? '') === 'admin' && ($targetUser['role'] ?? '') === 'developer') {
            $_SESSION['flash_error'] = 'Akun developer tidak dapat dihapus oleh admin.';
            return $this->redirect($usersPath);
        }

        $userModel->deleteById((int)$id);
        ActivityLogger::log($account['id'] ?? null, 'delete_user', 'users', (int)$id, 'Menghapus pengguna ID ' . $id);
        $_SESSION['flash_success'] = 'Pengguna dihapus.';
        return $this->redirect($usersPath);
    }

    private function collectData(bool $passwordRequired = true): array
    {
        $role = $_POST['role'] ?? 'mahasiswa';
        if (!in_array($role, ['admin','developer','dosen','mahasiswa'], true)) {
            $role = 'mahasiswa';
        }

        $password = $_POST['password'] ?? '';
        $passwordHash = null;
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        } elseif ($passwordRequired) {
            $passwordHash = null;
        }

        return [
            'username'      => trim($_POST['username'] ?? '') ?: null,
            'nama_lengkap'  => trim($_POST['nama_lengkap'] ?? ''),
            'email'         => trim($_POST['email'] ?? ''),
            'role'          => $role,
            'nim'           => $role === 'mahasiswa' ? trim($_POST['nim'] ?? '') : null,
            'nidn_nip'      => $role === 'dosen' ? trim($_POST['nidn_nip'] ?? '') : null,
            'password_hash' => $passwordHash,
        ];
    }
}
