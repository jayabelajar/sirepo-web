<?php

namespace App\Controllers\Api;

use App\Core\Mail\SmtpMailer;
use App\Models\ApiToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use Throwable;

class ForgotPasswordApiController extends BaseApiController
{
    private const TOKEN_TTL_MINUTES = 60;

    public function requestReset(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->enforceRateLimit('api_forgot_request_' . $ip, 8, 60, 300);

        $payload = array_merge($this->getJsonInput(), $_POST ?? []);
        $role = $this->normalizeRole((string) ($payload['role'] ?? 'mahasiswa'));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail('Email tidak valid.', 422);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email, $role);

        $issuedToken = null;
        if ($user) {
            $tokenModel = new PasswordResetToken();
            $tokenModel->cleanupExpired();
            $issuedToken = $tokenModel->createToken(
                (int) $user['id'],
                (string) ($user['email'] ?? $email),
                $role,
                self::TOKEN_TTL_MINUTES
            );

            $base = rtrim((string) ($this->config['base_url'] ?? ''), '/');
            $resetLink = $base . '/reset-password/' . rawurlencode($issuedToken);

            $subject = 'Permintaan Reset Password Akun SIREPO';
            $body = $this->buildResetBody((string) ($user['nama_lengkap'] ?? 'Pengguna'), $resetLink);

            try {
                $mailer = new SmtpMailer();
                $mailer->send((string) $user['email'], $subject, $body, (string) ($user['nama_lengkap'] ?? ''));
            } catch (Throwable $e) {
                @error_log('[API_FORGOT_PASSWORD] ' . $e->getMessage());
                $this->fail('Gagal mengirim email reset password.', 500);
            }
        }

        $response = [
            'message' => 'Jika email terdaftar, tautan reset password telah dikirim.',
        ];

        if (($this->config['environment'] ?? 'production') !== 'production' && $issuedToken !== null) {
            $response['debug_token'] = $issuedToken;
            $response['debug_reset_link'] = rtrim((string) ($this->config['base_url'] ?? ''), '/') . '/reset-password/' . rawurlencode($issuedToken);
        }

        $this->success($response);
    }

    public function verifyToken($token = null): void
    {
        $token = trim((string) ($token ?? ($_GET['token'] ?? '')));
        if ($token === '') {
            $this->fail('Token wajib diisi.', 422);
        }

        $tokenModel = new PasswordResetToken();
        $record = $tokenModel->findValidToken($token);
        if (!$record) {
            $this->fail('Token reset password tidak valid atau kedaluwarsa.', 404);
        }

        $this->success([
            'valid'      => true,
            'role'       => $record['role'] ?? null,
            'expires_at' => $record['expires_at'] ?? null,
        ]);
    }

    public function resetPassword(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->enforceRateLimit('api_forgot_reset_' . $ip, 10, 60, 300);

        $payload = array_merge($this->getJsonInput(), $_POST ?? []);
        $token = trim((string) ($payload['token'] ?? ''));
        $password = trim((string) ($payload['password'] ?? ''));
        $confirm = trim((string) ($payload['password_confirmation'] ?? ($payload['confirm_password'] ?? '')));

        if ($token === '' || $password === '' || $confirm === '') {
            $this->fail('token, password, dan password_confirmation wajib diisi.', 422);
        }

        if (strlen($password) < 8) {
            $this->fail('Password minimal 8 karakter.', 422);
        }

        if (!hash_equals($password, $confirm)) {
            $this->fail('Konfirmasi password tidak sama.', 422);
        }

        $tokenModel = new PasswordResetToken();
        $record = $tokenModel->findValidToken($token);
        if (!$record) {
            $this->fail('Token reset password tidak valid atau kedaluwarsa.', 404);
        }

        $userId = (int) ($record['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->fail('Token reset password tidak valid.', 400);
        }

        $userModel = new User();
        $userModel->updatePasswordHash($userId, password_hash($password, PASSWORD_DEFAULT));
        $userModel->resetLoginAttempts($userId);

        $tokenModel->markAsUsed((int) $record['id']);
        $tokenModel->markAllUserTokensAsUsed($userId, (string) ($record['role'] ?? 'mahasiswa'));

        $apiTokenModel = new ApiToken();
        $apiTokenModel->removeByUser($userId);

        $this->success([
            'message' => 'Password berhasil diubah. Silakan login kembali.',
        ]);
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
}
