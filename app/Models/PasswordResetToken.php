<?php

namespace App\Models;

use App\Core\Model;

class PasswordResetToken extends Model
{
    protected string $table = 'password_resets';

    public function createToken(int $userId, string $email, string $role, int $ttlMinutes = 60): string
    {
        $this->ensureTable();

        $role = $this->normalizeRole($role);
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + max(5, $ttlMinutes) * 60);

        $this->execute(
            "UPDATE {$this->table}
             SET used_at = NOW()
             WHERE user_id = :user_id AND role = :role AND used_at IS NULL",
            [
                'user_id' => $userId,
                'role' => $role,
            ]
        );

        $this->execute(
            "INSERT INTO {$this->table} (user_id, email, role, token_hash, expires_at, created_at)
             VALUES (:user_id, :email, :role, :token_hash, :expires_at, NOW())",
            [
                'user_id' => $userId,
                'email' => strtolower(trim($email)),
                'role' => $role,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt,
            ]
        );

        return $token;
    }

    public function findValidToken(string $token): ?array
    {
        $this->ensureTable();
        $tokenHash = hash('sha256', $token);

        return $this->fetch(
            "SELECT *
             FROM {$this->table}
             WHERE token_hash = :token_hash
               AND used_at IS NULL
               AND expires_at >= NOW()
             LIMIT 1",
            ['token_hash' => $tokenHash]
        );
    }

    public function markAsUsed(int $id): void
    {
        $this->ensureTable();
        $this->execute(
            "UPDATE {$this->table} SET used_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    public function markAllUserTokensAsUsed(int $userId, string $role): void
    {
        $this->ensureTable();
        $this->execute(
            "UPDATE {$this->table}
             SET used_at = NOW()
             WHERE user_id = :user_id AND role = :role AND used_at IS NULL",
            [
                'user_id' => $userId,
                'role' => $this->normalizeRole($role),
            ]
        );
    }

    public function cleanupExpired(): void
    {
        $this->ensureTable();
        $this->execute(
            "DELETE FROM {$this->table}
             WHERE expires_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
                OR (used_at IS NOT NULL AND used_at < DATE_SUB(NOW(), INTERVAL 7 DAY))"
        );
    }

    private function normalizeRole(string $role): string
    {
        return $role === 'dosen' ? 'dosen' : 'mahasiswa';
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            email VARCHAR(191) NOT NULL,
            role VARCHAR(32) NOT NULL,
            token_hash CHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_token_hash (token_hash),
            KEY idx_reset_user_role (user_id, role),
            KEY idx_reset_email_role (email, role),
            KEY idx_reset_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $this->query($sql);
    }
}
