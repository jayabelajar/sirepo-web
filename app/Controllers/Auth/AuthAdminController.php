<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Security\Auth;
use App\Core\Security\Csrf;
use App\Core\Helpers\ActivityLogger;

class AuthAdminController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login sebagai role lain, arahkan sesuai role
        if (isset($_SESSION['mahasiswa'])) {
            return $this->redirect('/mahasiswa/dashboard');
        }
        if (isset($_SESSION['dosen'])) {
            return $this->redirect('/dosen/dashboard');
        }
        if (isset($_SESSION['admin'])) {
            return $this->redirect('/admin/dashboard');
        }

        return $this->view('auth/login-admin', [
            'page_title' => 'Login Admin',
        ], 'auth');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/login/admin');
        }

        if (!Csrf::check($_POST['csrf_token'] ?? null)) {
            return $this->redirect('/login/admin?error=csrf');
        }

        $usernameOrEmail = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';

        $result = Auth::attempt($usernameOrEmail, $password, 'admin');
        if (!$result['success']) {
            $reason = $result['reason'] ?? 'invalid';
            if ($reason === 'throttled') {
                return $this->redirect('/login/admin?error=throttled');
            }
            if ($reason === 'banned') {
                return $this->redirect('/login/admin?error=banned');
            }
            return $this->redirect('/login/admin?error=invalid');
        }

        Auth::loginAdmin($result['user']);

        $lat = trim($_POST['latitude'] ?? '') ?: null;
        $lng = trim($_POST['longitude'] ?? '') ?: null;
        ActivityLogger::log(
            $result['user']['id'] ?? null,
            'login',
            null,
            null,
            'User berhasil login (admin)' . ($lat && $lng ? " [lokasi: {$lat},{$lng}]" : ''),
            $lat,
            $lng
        );

        // Arahkan ke dashboard admin
        return $this->redirect('/admin/dashboard');
    }
}
