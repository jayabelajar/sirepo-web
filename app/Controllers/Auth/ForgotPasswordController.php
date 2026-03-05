<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Mail\SmtpMailer;
use App\Core\Security\Csrf;
use App\Models\PasswordResetToken;
use App\Models\User;
use Throwable;

class ForgotPasswordController extends Controller
{
    private const TOKEN_TTL_MINUTES = 60;

    public function showMahasiswaForm()
    {
        return $this->showRequestForm('mahasiswa');
    }

    public function showDosenForm()
    {
        return $this->showRequestForm('dosen');
    }

    public function submitMahasiswa()
    {
        return $this->submitRequest('mahasiswa');
    }

    public function submitDosen()
    {
        return $this->submitRequest('dosen');
    }

    public function showResetForm(string $token)
    {
        $tokenModel = new PasswordResetToken();
        $record = $tokenModel->findValidToken($token);

        return $this->view('auth/reset-password', [
            'token_value' => $token,
            'is_valid_token' => $record !== null,
            'role' => $record['role'] ?? null,
            'page_title' => 'Atur Ulang Password',
        ], 'auth');
    }

    public function submitReset(string $token)
    {
        if (!Csrf::check($_POST['csrf_token'] ?? null)) {
            return $this->redirect('/reset-password/' . rawurlencode($token) . '?error=csrf');
        }

        $tokenModel = new PasswordResetToken();
        $record = $tokenModel->findValidToken($token);
        if (!$record) {
            return $this->redirect('/reset-password/' . rawurlencode($token) . '?error=token');
        }

        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirmation'] ?? '');

        if ($password === '' || $confirm === '') {
            return $this->redirect('/reset-password/' . rawurlencode($token) . '?error=empty');
        }

        if (strlen($password) < 8) {
            return $this->redirect('/reset-password/' . rawurlencode($token) . '?error=weak');
        }

        if (!hash_equals($password, $confirm)) {
            return $this->redirect('/reset-password/' . rawurlencode($token) . '?error=nomatch');
        }

        $userId = (int) ($record['user_id'] ?? 0);
        $role = $record['role'] ?? 'mahasiswa';
        if ($userId <= 0) {
            return $this->redirect('/reset-password/' . rawurlencode($token) . '?error=token');
        }

        $userModel = new User();
        $userModel->updatePasswordHash($userId, password_hash($password, PASSWORD_DEFAULT));
        $userModel->resetLoginAttempts($userId);

        $tokenModel->markAsUsed((int) $record['id']);
        $tokenModel->markAllUserTokensAsUsed($userId, $role);

        $loginPath = $this->loginPath($role);
        return $this->redirect($loginPath . '?reset=success');
    }

    private function showRequestForm(string $role)
    {
        return $this->view('auth/forgot-password', [
            'role' => $this->normalizeRole($role),
            'page_title' => 'Lupa Password',
        ], 'auth');
    }

    private function submitRequest(string $role)
    {
        $role = $this->normalizeRole($role);
        $formPath = $this->forgotPath($role);

        if (!Csrf::check($_POST['csrf_token'] ?? null)) {
            return $this->redirect($formPath . '?error=csrf');
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->redirect($formPath . '?error=email');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email, $role);

        if ($user) {
            $tokenModel = new PasswordResetToken();
            $tokenModel->cleanupExpired();
            $token = $tokenModel->createToken(
                (int) $user['id'],
                (string) ($user['email'] ?? $email),
                $role,
                self::TOKEN_TTL_MINUTES
            );

            $base = rtrim((require __DIR__ . '/../../../config/config.php')['base_url'] ?? '', '/');
            $resetLink = $base . '/reset-password/' . rawurlencode($token);

            $subject = 'Permintaan Reset Password Akun SIREPO';
            $body = $this->buildResetBody((string) ($user['nama_lengkap'] ?? 'Pengguna'), $resetLink);

            try {
                $mailer = new SmtpMailer();
                $mailer->send((string) $user['email'], $subject, $body, (string) ($user['nama_lengkap'] ?? ''));
            } catch (Throwable $e) {
                @error_log('[SMTP] ' . $e->getMessage());
                return $this->redirect($formPath . '?error=mail');
            }
        }

        return $this->redirect($formPath . '?status=sent');
    }

    private function buildResetBody(string $name, string $resetLink): string
    {
        return implode("\n", [
            'Yth. ' . trim($name) . ',',
            '',
            'Kami menerima permintaan untuk mengatur ulang password akun SIREPO Anda.',
            'Silakan gunakan tautan berikut untuk membuat password baru:',
            $resetLink,
            '',
            'Tautan berlaku selama ' . self::TOKEN_TTL_MINUTES . ' menit.',
            'Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.',
            '',
            'Salam,',
            'Tim SIREPO',
        ]);
    }

    private function normalizeRole(string $role): string
    {
        return $role === 'dosen' ? 'dosen' : 'mahasiswa';
    }

    private function loginPath(string $role): string
    {
        return $role === 'dosen' ? '/login/dosen' : '/login/mahasiswa';
    }

    private function forgotPath(string $role): string
    {
        return $role === 'dosen' ? '/lupa-password/dosen' : '/lupa-password/mahasiswa';
    }
}
