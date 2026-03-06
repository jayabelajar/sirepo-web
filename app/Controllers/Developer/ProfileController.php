<?php

namespace App\Controllers\Developer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Helpers\ActivityLogger;
use App\Core\Security\Auth;
use App\Core\Security\Csrf;
use App\Core\Security\Sanitizer;
use App\Models\User;

class ProfileController extends Controller
{
    private function requireDeveloper(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/login/developer');
        }

        $account = Auth::checkAdmin();
        if (($account['role'] ?? '') !== 'developer') {
            $this->redirect('/admin/dashboard');
        }
        return $account;
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
        $developer = $this->requireDeveloper();
        $userModel = new User();
        $userDb = $userModel->findById((int) $developer['id']);
        if ($userDb) {
            $developer = array_merge($userDb, $developer);
        }

        return $this->view('admin/profile', [
            'admin'              => $developer,
            'csrf'               => Csrf::token(),
            'header_activities'  => $this->headerActivities(),
            'panel_prefix'       => '/developer',
            'panel_brand_title'  => 'Developer Panel',
            'panel_user_label'   => 'Developer',
            'panel_role_label'   => 'Developer',
            'panel_logs_url'     => '/developer/dashboard',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => '/developer/dashboard'],
                ['label' => 'Profil'],
            ],
        ], 'admin');
    }

    public function update()
    {
        $developer = $this->requireDeveloper();
        Sanitizer::cleanRequest();

        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect('/developer/profile');
        }

        $userModel = new User();
        $data = [
            'nama_lengkap' => trim($_POST['nama_lengkap'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'username'     => trim($_POST['username'] ?? ''),
        ];

        $password = $_POST['password'] ?? '';
        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $userModel->updateProfile((int)$developer['id'], $data);

        $_SESSION['admin']['nama']  = $data['nama_lengkap'] ?: ($developer['nama'] ?? '');
        $_SESSION['admin']['email'] = $data['email'] ?: ($developer['email'] ?? '');

        ActivityLogger::log(
            $_SESSION['admin']['id'] ?? null,
            'update_profile',
            'users',
            (int)$developer['id'],
            'Perbarui profil developer'
        );

        $_SESSION['flash_success'] = 'Profil diperbarui.';
        return $this->redirect('/developer/profile');
    }
}
