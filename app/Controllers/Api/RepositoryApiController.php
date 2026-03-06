<?php

namespace App\Controllers\Api;

use App\Core\Security\Sanitizer;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Repository;
use App\Services\UploadService;

class RepositoryApiController extends BaseApiController
{
    private Repository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new Repository();
    }

    public function publicIndex(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->enforceRateLimit('api_public_repo_' . $ip, 120, 60, 120);

        $qRaw = $_GET['q'] ?? '';
        if (is_string($qRaw) && strlen($qRaw) > 150) {
            $this->fail('Parameter q terlalu panjang.', 400);
        }

        $filters = [
            'q'                => $this->sanitizeString($qRaw, 150),
            'tahun'            => isset($_GET['tahun']) ? (int) $_GET['tahun'] : null,
            'program_studi_id' => isset($_GET['prodi_id'])
                ? (int) $_GET['prodi_id']
                : (isset($_GET['program_studi_id']) ? (int) $_GET['program_studi_id'] : null),
            'mata_kuliah_id'   => isset($_GET['mk_id'])
                ? (int) $_GET['mk_id']
                : (isset($_GET['mata_kuliah_id'])
                    ? (int) $_GET['mata_kuliah_id']
                    : (isset($_GET['mk']) ? (int) $_GET['mk'] : null)),
            'jenis_karya'      => $this->sanitizeString($_GET['jenis_karya'] ?? ($_GET['jenis'] ?? ''), 50),
            'author'           => $this->sanitizeString($_GET['author'] ?? '', 150),
        ];

        if ($filters['tahun'] !== null && $filters['tahun'] < 1900) {
            $this->fail('Parameter tahun tidak valid.', 422);
        }

        $page    = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 10);
        [$page, $perPage, $offset] = $this->validatePagination($page, $perPage, 50);

        $items = $this->repo->searchPublic($filters, $perPage, $offset);
        $total = $this->repo->countPublic($filters);

        $this->success([
            'items' => $this->mapPublicItems($items),
            'meta'  => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'has_next'  => ($offset + count($items)) < $total,
            ]
        ]);
    }

    public function publicShow($idOrSlug): void
    {
        $clean = $this->sanitizeIdOrSlug($idOrSlug);
        if ($clean === null) {
            $this->fail('Parameter tidak valid.', 400);
        }

        $item = $this->repo->findPublicByIdOrSlug($clean);
        if (!$item && !ctype_digit($clean)) {
            // Fallback untuk slug lama/legacy yang formatnya tidak konsisten.
            $alternatives = [];
            $lower = strtolower($clean);
            if ($lower !== $clean) {
                $alternatives[] = $lower;
            }

            $slugified = trim((string) preg_replace('/\s+/', '-', $lower), '-');
            if ($slugified !== '' && $slugified !== $clean && $slugified !== $lower) {
                $alternatives[] = $slugified;
            }

            foreach ($alternatives as $alt) {
                $item = $this->repo->findPublicByIdOrSlug($alt);
                if ($item) {
                    break;
                }
            }
        }

        if (!$item) {
            $raw = ctype_digit($clean)
                ? $this->repo->findById((int) $clean)
                : $this->repo->getBySlug($clean);

            if ($raw && !$this->repo->isPublicAccessible($raw)) {
                $this->fail('Repository tidak tersedia untuk publik.', 403);
            }

            $this->fail('Repository tidak ditemukan.', 404);
        }

        $item = $this->appendPublicLinks($item);
        $related = $this->findRelatedItems($item, 4);

        $this->success([
            'item'    => $item,
            'related' => $related,
        ]);
    }

    public function publicByCategory(string $type, string $value): void
    {
        $normalizedType = $this->normalizeCategoryType($type);
        if ($normalizedType === null) {
            $this->fail('Jenis kategori tidak didukung.', 404);
        }

        $filters = $this->buildCategoryFilters($normalizedType, $value);

        $page    = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 10);
        [$page, $perPage, $offset] = $this->validatePagination($page, $perPage, 50);

        $items = $this->repo->searchPublic($filters, $perPage, $offset);
        $total = $this->repo->countPublic($filters);

        $this->success([
            'category' => $this->resolveCategoryMetadata($normalizedType, $filters),
            'items'    => $this->mapPublicItems($items),
            'meta'     => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'has_next'  => ($offset + count($items)) < $total,
            ],
        ]);
    }

    public function download($id): void
    {
        $clean = $this->sanitizeIdOrSlug($id);
        if ($clean === null || !ctype_digit($clean)) {
            $this->fail('Parameter tidak valid.', 400);
        }

        $repo = $this->repo->findById((int) $clean);
        if (!$repo) {
            $this->fail('Repository tidak ditemukan.', 404);
        }

        if (!$this->repo->isPublicAccessible($repo)) {
            $this->fail('Repository tidak tersedia untuk publik.', 403);
        }

        if (empty($repo['file_pdf'])) {
            $this->fail('File belum tersedia.', 404);
        }

        $this->streamPdf($repo['file_pdf']);
    }

    public function dosenIndex(): void
    {
        $user = $this->currentUser('dosen');
        $page    = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 10);
        [$page, $perPage, $offset] = $this->validatePagination($page, $perPage, 50);

        $filters = [
            'q'                => $this->sanitizeString($_GET['q'] ?? '', 150),
            'tahun'            => isset($_GET['tahun']) ? (int) $_GET['tahun'] : null,
            'program_studi_id' => isset($_GET['prodi_id'])
                ? (int) $_GET['prodi_id']
                : (isset($_GET['program_studi_id']) ? (int) $_GET['program_studi_id'] : null),
            'mata_kuliah_id'   => isset($_GET['mk_id'])
                ? (int) $_GET['mk_id']
                : (isset($_GET['mata_kuliah_id'])
                    ? (int) $_GET['mata_kuliah_id']
                    : (isset($_GET['mk']) ? (int) $_GET['mk'] : null)),
            'jenis_karya'      => $this->sanitizeString($_GET['jenis_karya'] ?? ($_GET['jenis'] ?? ''), 50),
            'author'           => $this->sanitizeString($_GET['author'] ?? '', 150),
        ];

        $items = $this->repo->filterByUser((int) $user['id'], $filters, $perPage, $offset);
        $total = $this->repo->countFilteredByUser((int) $user['id'], $filters);

        $this->success([
            'items' => $items,
            'meta'  => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'has_next'  => ($offset + count($items)) < $total,
            ]
        ]);
    }

    public function store(): void
    {
        $user = $this->currentUser('dosen');
        $payload = array_merge($this->getJsonInput(), $_POST ?? []);
        $data = $this->validateRepositoryPayload($payload, $user);

        $created = $this->repo->createOwned((int) $user['id'], $data);
        if (!$created) {
            $this->fail('Gagal membuat repository.', 500);
        }

        $this->success(['item' => $created], 201);
    }

    public function show($id): void
    {
        $user = $this->currentUser('dosen');
        $repo = $this->ensureOwnership((int) $id, (int) $user['id']);
        $this->success(['item' => $repo]);
    }

    public function update($id): void
    {
        $user = $this->currentUser('dosen');
        $existing = $this->ensureOwnership((int) $id, (int) $user['id']);

        $payload = array_merge($this->getJsonInput(), $_POST ?? []);
        $data = $this->validateRepositoryPayload($payload, $user, $existing);

        if (!$this->repo->updateOwned((int) $user['id'], (int) $existing['id'], $data)) {
            $this->fail('Gagal memperbarui repository.', 500);
        }

        $updated = $this->repo->findById((int) $existing['id']);
        $this->success(['item' => $updated]);
    }

    public function destroy($id): void
    {
        $user = $this->currentUser('dosen');
        $repo = $this->ensureOwnership((int) $id, (int) $user['id']);

        if (!$this->repo->deleteOwned((int) $user['id'], (int) $repo['id'])) {
            $this->fail('Gagal menghapus repository.', 500);
        }

        $this->success(['message' => 'Repository dihapus.']);
    }

    public function uploadFile($id): void
    {
        $user = $this->currentUser('dosen');
        $repo = $this->ensureOwnership((int) $id, (int) $user['id']);

        if (empty($_FILES['file'])) {
            $this->fail('File wajib diunggah.', 422);
        }

        $uploader = new UploadService('uploads');
        try {
            $stored = $uploader->storePdf($_FILES['file']);
        } catch (\RuntimeException $e) {
            $this->fail($e->getMessage(), 422);
        }

        if (!empty($repo['file_pdf'])) {
            $this->safeDeleteOldFile($repo['file_pdf']);
        }

        if (!$this->repo->updateFile((int) $repo['id'], $stored)) {
            $this->fail('Gagal menyimpan file.', 500);
        }

        $this->success([
            'item' => $this->repo->findById((int) $repo['id']),
        ]);
    }

    private function sanitizeIdOrSlug($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (strlen($value) > 191) {
            return null;
        }

        // Normalisasi ID numerik (contoh "12", "12.0")
        if (preg_match('/^\d+$/', $value)) {
            return (string) ((int) $value);
        }
        if (preg_match('/^\d+\.0+$/', $value)) {
            return (string) ((int) floor((float) $value));
        }

        // Jika Flutter tidak sengaja mengirim URL penuh, ambil segmen terakhirnya.
        if (preg_match('~^https?://~i', $value)) {
            $path = parse_url($value, PHP_URL_PATH);
            if (is_string($path) && $path !== '') {
                $lastSegment = trim((string) basename($path));
                if ($lastSegment !== '' && preg_match('/^\d+$/', $lastSegment)) {
                    return (string) ((int) $lastSegment);
                }
            }
        }

        // Handle format "/api/mobile/public/repositories/12" atau "repositories/12?x=1"
        $trimmedPath = trim($value, "/ \t\n\r\0\x0B");
        if (strpos($trimmedPath, '/') !== false) {
            $tail = (string) preg_replace('/\?.*$/', '', $trimmedPath);
            $tail = trim((string) basename($tail));
            if ($tail !== '' && preg_match('/^\d+$/', $tail)) {
                return (string) ((int) $tail);
            }
        }

        if (strpos($value, "\0") !== false) {
            return null;
        }

        return $value;
    }

    private function mapPublicItems(array $items): array
    {
        return array_map(function ($item) {
            return $this->appendPublicLinks($item);
        }, $items);
    }

    private function appendPublicLinks(array $item): array
    {
        $base = rtrim((string) ($this->config['base_url'] ?? ''), '/');
        $id = (int) ($item['id'] ?? 0);
        $slug = trim((string) ($item['slug'] ?? ''));
        $identifier = '';

        if ($id > 0) {
            $item['download_url'] = $base . '/api/mobile/public/repositories/' . $id . '/download';
            $identifier = (string) $id;
            $item['identifier_type'] = 'id';
        }

        if ($slug !== '') {
            $item['web_url'] = $base . '/repository/' . rawurlencode($slug);
            $item['slug_value'] = $slug;
            if ($identifier === '') {
                // Fallback kalau data lama tidak punya id valid.
                $identifier = $slug;
                $item['identifier_type'] = 'slug';
            }
        }

        if ($identifier !== '') {
            $item['identifier'] = $identifier;
            $item['detail_url'] = $base . '/api/mobile/public/repositories/' . rawurlencode($identifier);
        }

        $item['has_file'] = !empty($item['file_pdf']);

        return $item;
    }

    private function findRelatedItems(array $item, int $limit = 4): array
    {
        $filters = [];
        if (!empty($item['program_studi_id'])) {
            $filters['program_studi_id'] = (int) $item['program_studi_id'];
        } elseif (!empty($item['jenis_karya'])) {
            $filters['jenis_karya'] = (string) $item['jenis_karya'];
        } elseif (!empty($item['tahun'])) {
            $filters['tahun'] = (int) $item['tahun'];
        } else {
            return [];
        }

        $candidates = $this->repo->searchPublic($filters, $limit + 1, 0);
        $related = [];
        $currentId = (int) ($item['id'] ?? 0);

        foreach ($candidates as $candidate) {
            if ((int) ($candidate['id'] ?? 0) === $currentId) {
                continue;
            }
            $related[] = $this->appendPublicLinks($candidate);
            if (count($related) >= $limit) {
                break;
            }
        }

        return $related;
    }

    private function normalizeCategoryType(string $type): ?string
    {
        $type = strtolower(trim($type));
        switch ($type) {
            case 'program-studi':
            case 'program_studi':
            case 'prodi':
                return 'program_studi';
            case 'mata-kuliah':
            case 'mata_kuliah':
            case 'mk':
                return 'mata_kuliah';
            case 'tahun':
            case 'year':
                return 'tahun';
            case 'jenis-karya':
            case 'jenis_karya':
            case 'jenis':
                return 'jenis_karya';
            case 'author':
            case 'penulis':
                return 'author';
            default:
                return null;
        }
    }

    private function buildCategoryFilters(string $type, string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            $this->fail('Nilai kategori wajib diisi.', 422);
        }

        switch ($type) {
            case 'program_studi':
                if (!ctype_digit($value)) {
                    $this->fail('Kategori program studi harus berupa ID numerik.', 422);
                }
                return ['program_studi_id' => (int) $value];

            case 'mata_kuliah':
                if (!ctype_digit($value)) {
                    $this->fail('Kategori mata kuliah harus berupa ID numerik.', 422);
                }
                return ['mata_kuliah_id' => (int) $value];

            case 'tahun':
                if (!ctype_digit($value)) {
                    $this->fail('Kategori tahun harus berupa angka.', 422);
                }
                $year = (int) $value;
                if ($year < 1900 || $year > ((int) date('Y') + 1)) {
                    $this->fail('Nilai tahun tidak valid.', 422);
                }
                return ['tahun' => $year];

            case 'jenis_karya':
                $jenis = $this->sanitizeString($value, 50);
                if ($jenis === '') {
                    $this->fail('Nilai jenis karya tidak valid.', 422);
                }
                return ['jenis_karya' => $jenis];

            case 'author':
                $author = $this->sanitizeString($value, 150);
                if ($author === '') {
                    $this->fail('Nilai author tidak valid.', 422);
                }
                return ['author' => $author];
        }

        $this->fail('Jenis kategori tidak didukung.', 404);
    }

    private function resolveCategoryMetadata(string $type, array $filters): array
    {
        switch ($type) {
            case 'program_studi':
                $prodiId = (int) ($filters['program_studi_id'] ?? 0);
                $prodi = (new ProgramStudi())->find($prodiId);
                if (!$prodi) {
                    $this->fail('Program studi tidak ditemukan.', 404);
                }
                return [
                    'type'  => 'program_studi',
                    'value' => (string) $prodiId,
                    'label' => (string) ($prodi['nama_program_studi'] ?? ''),
                ];

            case 'mata_kuliah':
                $mkId = (int) ($filters['mata_kuliah_id'] ?? 0);
                $mk = (new MataKuliah())->find($mkId);
                if (!$mk) {
                    $this->fail('Mata kuliah tidak ditemukan.', 404);
                }
                return [
                    'type'  => 'mata_kuliah',
                    'value' => (string) $mkId,
                    'label' => (string) ($mk['nama'] ?? ''),
                ];

            case 'tahun':
                $year = (int) ($filters['tahun'] ?? 0);
                return [
                    'type'  => 'tahun',
                    'value' => (string) $year,
                    'label' => (string) $year,
                ];

            case 'jenis_karya':
                $jenis = (string) ($filters['jenis_karya'] ?? '');
                return [
                    'type'  => 'jenis_karya',
                    'value' => $jenis,
                    'label' => $jenis,
                ];

            case 'author':
                $author = (string) ($filters['author'] ?? '');
                return [
                    'type'  => 'author',
                    'value' => $author,
                    'label' => $author,
                ];
        }

        return [
            'type' => $type,
            'value' => '',
            'label' => '',
        ];
    }

    private function validateRepositoryPayload(array $payload, array $user, ?array $existing = null): array
    {
        $judul = $this->sanitizeString($payload['judul'] ?? ($existing['judul'] ?? ''), 200);
        if ($judul === '') {
            $this->fail('Judul wajib diisi.', 422);
        }

        $slugInput = $payload['slug'] ?? null;
        $slug = $slugInput ? strtolower(trim($slugInput)) : $this->generateSlug($judul);
        if (!$this->isValidSlug($slug)) {
            $this->fail('Slug tidak valid.', 422);
        }
        $slug = $this->ensureUniqueSlug($slug, $existing['id'] ?? null);

        $tahun = isset($payload['tahun']) ? (int) $payload['tahun'] : (int) ($existing['tahun'] ?? date('Y'));
        $currentYear = (int) date('Y') + 1;
        if ($tahun < 1900 || $tahun > $currentYear) {
            $this->fail('Tahun tidak valid.', 422);
        }

        $jenisKarya = $this->sanitizeString($payload['jenis_karya'] ?? ($existing['jenis_karya'] ?? 'lainnya'), 50);
        $author = $this->sanitizeString(
            $payload['author'] ?? ($existing['author'] ?? ($user['nama_lengkap'] ?? '')),
            150
        );

        $prodi = isset($payload['program_studi_id']) ? (int) $payload['program_studi_id']
            : (isset($payload['prodi_id']) ? (int) $payload['prodi_id'] : ($existing['program_studi_id'] ?? null));
        if ($prodi !== null && $prodi <= 0) {
            $prodi = null;
        }

        $mk = isset($payload['mata_kuliah_id']) ? (int) $payload['mata_kuliah_id'] : ($existing['mata_kuliah_id'] ?? null);
        if ($mk !== null && $mk <= 0) {
            $mk = null;
        }

        $abstrak = trim((string) ($payload['abstrak'] ?? ($existing['abstrak'] ?? '')));
        if ($abstrak !== '') {
            $abstrak = strip_tags($abstrak);
        } else {
            $abstrak = null;
        }

        $keywords = $this->sanitizeString($payload['keywords'] ?? ($existing['keywords'] ?? ''), 200);
        if ($keywords === '') {
            $keywords = null;
        }

        return [
            'judul'            => $judul,
            'slug'             => $slug,
            'jenis_karya'      => $jenisKarya ?: 'lainnya',
            'author'           => $author,
            'tahun'            => $tahun,
            'program_studi_id' => $prodi,
            'mata_kuliah_id'   => $mk,
            'abstrak'          => $abstrak,
            'file_pdf'         => $existing['file_pdf'] ?? null,
            'uploaded_by'      => (int) $user['id'],
            'keywords'         => $keywords,
        ];
    }

    private function ensureOwnership(int $repoId, int $userId): array
    {
        $item = $this->repo->findOwnedByDosen($userId, $repoId);
        if (!$item) {
            $this->fail('Repository tidak ditemukan atau bukan milik Anda.', 404);
        }

        return $item;
    }

    private function isValidSlug(string $slug): bool
    {
        return (bool) preg_match('/^[a-z0-9-]+$/', $slug);
    }

    private function generateSlug(string $source): string
    {
        $text = strtolower($source);
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim((string) $text, '-');

        if (strlen($text) > 80) {
            $text = substr($text, 0, 80);
            $text = rtrim($text, '-');
        }

        return $text === '' ? bin2hex(random_bytes(4)) : $text;
    }

    private function ensureUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $counter = 0;

        while (true) {
            $candidate = $counter === 0 ? $base : $base . '-' . $counter;
            $exists = $this->repo->getBySlug($candidate);

            if (!$exists || ($ignoreId !== null && (int) $exists['id'] === $ignoreId)) {
                return $candidate;
            }

            $counter++;
            if ($counter > 20) {
                return $base . '-' . bin2hex(random_bytes(3));
            }
        }
    }

    private function streamPdf(string $filename): void
    {
        $baseDir = realpath(__DIR__ . '/../../../storage/uploads');
        if ($baseDir === false) {
            $this->fail('File tidak ditemukan.', 404);
        }

        $path = realpath($baseDir . '/' . $filename);
        if (!$path || strpos($path, $baseDir) !== 0 || !is_file($path)) {
            $this->fail('File tidak ditemukan.', 404);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }

    private function safeDeleteOldFile(string $filename): void
    {
        $baseDir = realpath(__DIR__ . '/../../../storage/uploads');
        if ($baseDir === false) {
            return;
        }

        $path = realpath($baseDir . '/' . $filename);
        if ($path && strpos($path, $baseDir) === 0 && is_file($path)) {
            @unlink($path);
        }
    }
}
