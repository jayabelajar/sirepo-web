<?php

namespace App\Controllers\Mahasiswa;

use App\Core\Controller;
use App\Core\Security\Auth;
use App\Models\Repository;
use App\Models\ActivityLog;
use App\Core\Helpers\ActivityLogger;

class MyRepositoryController extends Controller
{
    public function index()
    {
        $mhs = Auth::checkMahasiswa();
        $repoModel = new Repository();
        $logModel  = new ActivityLog();

        $filters = [
            'q'      => trim($_GET['q'] ?? ''),
            'tahun'  => $_GET['tahun'] ?? '',
            'jenis_karya' => $_GET['jenis'] ?? '',
        ];

        $repos = $repoModel->filterByUser((int) $mhs['id'], $filters, 100, 0);
        $headerActivities = array_map(static function ($row) use ($mhs) {
            return [
                'actor'  => $mhs['nama'] ?? $mhs['nama_lengkap'] ?? 'Mahasiswa',
                'action' => $row['description'] ?? $row['activity_type'],
                'time'   => $row['created_at'] ?? '',
            ];
        }, $logModel->getRecentByUser((int)$mhs['id'], 5));

        $this->view('mahasiswa/my_repository', [
            'user'  => $mhs,
            'mhs'   => $mhs,
            'repos' => $repos,
            'filters' => $filters,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => '/mahasiswa/dashboard'],
                ['label' => 'My Repository'],
            ],
            'header_activities' => $headerActivities,
            'suppress_layout_title' => true,
        ], 'mahasiswa');
    }

    public function create()
    {
        $mhs = Auth::checkMahasiswa();
        $prodiModel = new \App\Models\ProgramStudi();
        $mkModel    = new \App\Models\MataKuliah();
        $userModel  = new \App\Models\User();

        $this->view('mahasiswa/my_repository_form', [
            'mhs'        => $mhs,
            'relations'  => [],
            'prodis'     => $prodiModel->getAllWithCount(),
            'mk_list'    => $mkModel->getAll('', 'asc', 1000, 0),
            'mahasiswas' => $userModel->getAllOrdered('', 'mahasiswa', 5000, 0),
            'dosens'     => $userModel->getAllOrdered('', 'dosen', 5000, 0),
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => '/mahasiswa/dashboard'],
                ['label' => 'My Repository', 'url' => '/mahasiswa/my-repository'],
                ['label' => 'Unggah Mandiri'],
            ],
            'suppress_layout_title' => true,
        ], 'mahasiswa');
    }

    public function store()
    {
        $mhs = Auth::checkMahasiswa();
        $repoModel = new Repository();
        $logModel  = new ActivityLog();

        $judul = trim($_POST['judul'] ?? '');
        $jenis = $_POST['jenis_karya'] ?? '';
        $tahun = (int)($_POST['tahun'] ?? date('Y'));
        $prodi = (int)($_POST['program_studi_id'] ?? 0);
        $mk    = (int)($_POST['mata_kuliah_id'] ?? 0);
        $abstrak = trim($_POST['abstrak'] ?? '');
        $keywords = trim($_POST['keywords'] ?? '');

        if (!$judul || !$jenis) {
            return $this->redirect('/mahasiswa/my-repository/create?error=Judul dan Jenis Karya wajib diisi');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
        $slug = trim($slug, '-');
        
        // Ensure unique slug
        $check = $repoModel->getBySlug($slug);
        if ($check) {
            $slug .= '-' . time();
        }

        $fileName = null;
        if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploader = new \App\Services\UploadService();
                $fileName = $uploader->storePdf($_FILES['file_pdf']);
            } catch (\RuntimeException $e) {
                return $this->redirect('/mahasiswa/my-repository/create?error=' . urlencode($e->getMessage()));
            }
        }

        $data = [
            'judul'            => $judul,
            'slug'             => $slug,
            'jenis_karya'      => $jenis,
            'author'           => (!empty($_POST['author'])) ? $_POST['author'] : ($mhs['nama_lengkap'] ?? $mhs['username']),
            'tahun'            => $tahun,
            'program_studi_id' => $prodi,
            'mata_kuliah_id'   => $mk,
            'abstrak'          => $abstrak,
            'file_pdf'         => $fileName,
            'uploaded_by'      => $mhs['id'],
            'keywords'         => $keywords,
            'status'           => 'pending' // Mahasiswa upload sets status to pending
        ];

        $repoModel->create($data);
        $repoId = (int)$repoModel->lastInsertId();
        
        // Collect tagged users
        $authors   = $_POST['authors'] ?? []; // Array of user IDs
        $advisors  = $_POST['advisors'] ?? []; // Array of user IDs
        $examiners = $_POST['examiners'] ?? []; // Array of user IDs

        // Ensure the uploader (mhs) is always one of the authors
        if (!in_array($mhs['id'], $authors)) {
            $authors[] = $mhs['id'];
        }
        
        // Attach user relations
        $repoModel->syncUserRelations($repoId, $authors, $advisors, $examiners, $mhs['id']);

        ActivityLogger::log((int)$mhs['id'], 'REPOSITORY_UPLOAD', 'repository', $repoId, "Mengunggah repository mandiri: $judul");

        return $this->redirect('/mahasiswa/my-repository?success=Berhasil mengunggah. Menunggu review admin.');
    }

    public function edit(int $id)
    {
        return $this->redirect('/mahasiswa/my-repository?error=Hanya admin yang dapat mengubah repository');
    }

    public function update(int $id)
    {
        return $this->redirect('/mahasiswa/my-repository?error=Hanya admin yang dapat mengubah repository');
    }

    public function delete(int $id)
    {
        return $this->redirect('/mahasiswa/my-repository?error=Hanya admin yang dapat menghapus repository');
    }

}
