<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Helpers\ActivityLogger;
use App\Core\Security\Auth;
use App\Core\Security\Csrf;

class AuthDeveloperController extends Controller
{
    public function showLoginForm()
    {
        if (isset($_SESSION['mahasiswa'])) {
            return $this->redirect('/mahasiswa/dashboard');
        }
        if (isset($_SESSION['dosen'])) {
            return $this->redirect('/dosen/dashboard');
        }
        if (isset($_SESSION['admin'])) {
            $role = $_SESSION['admin']['role'] ?? 'admin';
            return $this->redirect($role === 'developer' ? '/developer/dashboard' : '/admin/dashboard');
        }

        return $this->view('auth/login-developer', [
            'page_title' => 'Login Developer',
        ], 'auth');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/login/developer');
        }

        if (!Csrf::check($_POST['csrf_token'] ?? null)) {
            return $this->redirect('/login/developer?error=csrf');
        }

        $usernameOrEmail = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';

        $result = Auth::attempt($usernameOrEmail, $password, 'developer');
        if (!$result['success']) {
            $reason = $result['reason'] ?? 'invalid';
            if ($reason === 'throttled') {
                return $this->redirect('/login/developer?error=throttled');
            }
            if ($reason === 'banned') {
                return $this->redirect('/login/developer?error=banned');
            }
            return $this->redirect('/login/developer?error=invalid');
        }

        // Reuse session admin so seluruh kontrol panel tetap kompatibel.
        Auth::loginAdmin($result['user']);

        $lat = trim($_POST['latitude'] ?? '') ?: null;
        $lng = trim($_POST['longitude'] ?? '') ?: null;
        ActivityLogger::log(
            $result['user']['id'] ?? null,
            'login',
            null,
            null,
            'User berhasil login (developer)' . ($lat && $lng ? " [lokasi: {$lat},{$lng}]" : ''),
            $lat,
            $lng
        );

        return $this->redirect('/developer/dashboard');
    }
}

