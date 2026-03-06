<?php

namespace App\Controllers\Developer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security\Auth;

class DashboardController extends Controller
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

    private function recentActivities(int $limit = 10): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT al.description, al.activity_type, al.created_at, u.nama_lengkap
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(static function ($row) {
            return [
                'actor'  => $row['nama_lengkap'] ?: 'System',
                'action' => $row['description'] ?: $row['activity_type'],
                'time'   => $row['created_at'],
            ];
        }, $rows ?: []);
    }

    private function repoPerYear(): array
    {
        $db = Database::getConnection();
        $rows = $db->query("
            SELECT tahun AS label, COUNT(*) AS total
            FROM repository
            GROUP BY tahun
            ORDER BY tahun ASC
        ")->fetchAll();

        $labels = [];
        $data   = [];
        foreach ($rows as $row) {
            $labels[] = $row['label'];
            $data[]   = (int) $row['total'];
        }
        return [$labels, $data];
    }

    private function repoByJenis(): array
    {
        $db = Database::getConnection();
        $rows = $db->query("
            SELECT jenis_karya AS label, COUNT(*) AS total
            FROM repository
            GROUP BY jenis_karya
            ORDER BY jenis_karya ASC
        ")->fetchAll();

        $labels = [];
        $data   = [];
        foreach ($rows as $row) {
            $labels[] = ucfirst(str_replace('_', ' ', $row['label']));
            $data[]   = (int) $row['total'];
        }
        return [$labels, $data];
    }

    private function countTable(string $table): int
    {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
    }

    public function index()
    {
        $developer = $this->requireDeveloper();

        [$chartYearLabels, $chartYearData] = $this->repoPerYear();
        [$chartJenisLabels, $chartJenisData] = $this->repoByJenis();
        $activities = $this->recentActivities(10);

        $stats = [
            'total_repository' => $this->countTable('repository'),
            'total_prodi'      => $this->countTable('program_studi'),
            'total_mk'         => $this->countTable('mata_kuliah'),
            'total_user'       => $this->countTable('users'),
        ];

        // View admin/dashboard dipakai ulang agar style konsisten.
        return $this->view('admin/dashboard', [
            'admin'               => $developer,
            'stats'               => $stats,
            'activities'          => $activities,
            'header_activities'   => $activities,
            'chart_year_labels'   => $chartYearLabels,
            'chart_year_data'     => $chartYearData,
            'chart_jenis_labels'  => $chartJenisLabels,
            'chart_jenis_data'    => $chartJenisData,
            'panel_prefix'        => '/developer',
            'panel_brand_title'   => 'Developer Panel',
            'panel_user_label'    => 'Developer',
            'panel_role_label'    => 'Developer',
            'panel_show_create_repo' => false,
            'panel_logs_url'      => '/developer/dashboard',
            'breadcrumb'          => [],
        ], 'admin');
    }
}
