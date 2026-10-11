<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class Setting extends Model
{
    protected string $table = 'settings';
    private bool $tableChecked = false;

    public function get(): ?array
    {
        $this->ensureTable();

        try {
            return $this->fetch("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT 1");
        } catch (PDOException $e) {
            if ($this->isMissingTable($e)) {
                return $this->defaultSetting();
            }

            throw $e;
        }
    }

    public function updateMaintenance(bool $on): bool
    {
        $this->ensureTable();

        $row = $this->get();
        if ($row) {
            return $this->execute("UPDATE {$this->table} SET maintenance_mode = :m WHERE id = :id", [
                'm'  => $on ? 1 : 0,
                'id' => $row['id']
            ]);
        }
        return $this->execute("INSERT INTO {$this->table} (maintenance_mode) VALUES (:m)", ['m' => $on ? 1 : 0]);
    }

    private function ensureTable(): void
    {
        if ($this->tableChecked) {
            return;
        }

        $this->tableChecked = true;

        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS {$this->table} (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    maintenance_mode TINYINT(1) NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (PDOException $e) {
            if ($this->isMissingTable($e)) {
                return;
            }

            throw $e;
        }
    }

    private function defaultSetting(): array
    {
        return [
            'id' => null,
            'maintenance_mode' => 0,
        ];
    }

    private function isMissingTable(PDOException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1146 || $e->getCode() === '42S02';
    }
}
