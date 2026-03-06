<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security\Auth;
use App\Core\Security\Csrf;
use App\Core\Security\Sanitizer;
use App\Models\Setting;
use App\Core\Database;
use App\Core\Helpers\ActivityLogger;

class SettingController extends Controller
{
    private function panelPrefix(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        return strpos($path, '/developer') === 0 ? '/developer' : '/admin';
    }

    private function settingsPath(): string
    {
        return $this->panelPrefix() . '/settings';
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
        $settingModel = new Setting();
        $setting = $settingModel->get();

        return $this->view('admin/setting', [
            'admin'      => $account,
            'setting'    => $setting,
            'csrf'       => Csrf::token(),
            'header_activities' => $this->headerActivities(),
            'panel_prefix' => $panelPrefix,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => $panelPrefix . '/dashboard'],
                ['label' => 'Pengaturan'],
            ],
        ], 'admin');
    }

    public function maintenance()
    {
        $account = Auth::checkAdmin();
        $settingsPath = $this->settingsPath();
        Sanitizer::cleanRequest();

        if (!Csrf::check($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Sesi kadaluarsa.';
            return $this->redirect($settingsPath);
        }

        $on = isset($_POST['maintenance']) && $_POST['maintenance'] == '1';
        $settingModel = new Setting();
        $settingModel->updateMaintenance($on);
        ActivityLogger::log($account['id'] ?? null, 'maintenance_toggle', 'settings', null, $on ? 'Aktifkan maintenance' : 'Nonaktifkan maintenance');

        $_SESSION['flash_success'] = 'Pengaturan maintenance diperbarui.';
        return $this->redirect($settingsPath);
    }

    public function backup()
    {
        $account = Auth::checkAdmin();
        $settingsPath = $this->settingsPath();
        $config = require __DIR__ . '/../../../config/config.php';
        $dbCfg = $config['database'] ?? [];
        $host  = $dbCfg['host'] ?? '127.0.0.1';
        $port  = $dbCfg['port'] ?? 3306;
        $name  = $dbCfg['name'] ?? '';
        $user  = $dbCfg['user'] ?? '';
        $pass  = $dbCfg['pass'] ?? '';

        $mysqli = @new \mysqli($host, $user, $pass, $name, (int) $port);
        if ($mysqli->connect_error) {
            $_SESSION['flash_error'] = 'Koneksi database gagal: ' . $mysqli->connect_error;
            return $this->redirect($settingsPath);
        }
        $mysqli->set_charset('utf8mb4');

        $sqlDump  = "-- Backup database: {$name}\n";
        $sqlDump .= "-- Tanggal: " . date('Y-m-d H:i:s') . "\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tablesRes = $mysqli->query("SHOW TABLES");
        if (!$tablesRes) {
            $_SESSION['flash_error'] = 'Gagal mengambil daftar tabel: ' . $mysqli->error;
            return $this->redirect($settingsPath);
        }

        while ($row = $tablesRes->fetch_array(MYSQLI_NUM)) {
            $table = $row[0];
            $sqlDump .= "-- -------------------------------------------\n";
            $sqlDump .= "-- Struktur tabel `$table`\n";
            $sqlDump .= "-- -------------------------------------------\n\n";
            $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";

            $createRes = $mysqli->query("SHOW CREATE TABLE `$table`");
            if ($createRes) {
                $createRow = $createRes->fetch_array(MYSQLI_NUM);
                $createSql = $createRow[1] ?? null;
                if ($createSql) {
                    $sqlDump .= $createSql . ";\n\n";
                }
                $createRes->free();
            }

            $dataRes = $mysqli->query("SELECT * FROM `$table`");
            if ($dataRes && $dataRes->num_rows > 0) {
                $sqlDump .= "-- Data untuk tabel `$table`\n\n";
                while ($data = $dataRes->fetch_assoc()) {
                    $columns = array_keys($data);
                    $values = array_map(function ($value) use ($mysqli) {
                        if ($value === null) {
                            return "NULL";
                        }
                        return "'" . $mysqli->real_escape_string($value) . "'";
                    }, array_values($data));

                    $sqlDump .= "INSERT INTO `$table` (`"
                        . implode('`,`', $columns)
                        . "`) VALUES ("
                        . implode(',', $values)
                        . ");\n";
                }
                $sqlDump .= "\n";
                $dataRes->free();
            }

            $sqlDump .= "\n\n";
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $tablesRes->free();
        $mysqli->close();

        $filename = 'backup-' . $name . '-' . date('Ymd-His') . '.sql';
        ActivityLogger::log($account['id'] ?? null, 'backup_db', 'settings', null, 'Unduh backup database');

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sqlDump));
        header('Pragma: public');
        header('Cache-Control: must-revalidate');
        header('Expires: 0');
        echo $sqlDump;
        exit;
    }
}
