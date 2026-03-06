<?php

namespace App\Controllers\Api;

use App\Models\Bookmark;
use App\Models\Repository;

class BookmarkApiController extends BaseApiController
{
    public function index(): void
    {
        $user = $this->requireUser();
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? ($_GET['limit'] ?? 20));
        [$page, $perPage, $offset] = $this->validatePagination($page, $perPage, 100);

        $bookmark = new Bookmark();
        $items = $bookmark->listByUser((int) $user['id'], $perPage, $offset);
        $total = $bookmark->countByUser((int) $user['id']);

        $this->success([
            'items' => $this->normalizeBookmarkItems($items),
            'meta'  => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'has_next'  => ($offset + count($items)) < $total,
            ],
        ]);
    }

    public function show($id): void
    {
        $user = $this->requireUser();
        $bookmarkId = (int) $id;
        if ($bookmarkId <= 0) {
            $this->fail('ID bookmark tidak valid.', 400);
        }

        $bookmark = new Bookmark();
        $item = $bookmark->findByIdForUser((int) $user['id'], $bookmarkId);
        if (!$item) {
            $this->fail('Bookmark tidak ditemukan.', 404);
        }

        $this->success(['item' => $this->normalizeBookmarkItem($item)]);
    }

    public function store(): void
    {
        $user = $this->requireUser();
        $payload = array_merge($this->getJsonInput(), $_POST ?? []);

        $repoModel = new Repository();
        $repoId = $this->resolveRepositoryId($payload, $repoModel);
        $repo = $repoModel->findById($repoId);

        if (!$repo) {
            $this->fail('Repository tidak ditemukan.', 404);
        }

        if (!$repoModel->isPublicAccessible($repo)) {
            $this->fail('Repository tidak tersedia untuk dibookmark.', 403);
        }

        $bookmark = new Bookmark();
        $existing = $bookmark->findByRepositoryForUser((int) $user['id'], $repoId);
        if ($existing) {
            $this->success([
                'item'       => $this->normalizeBookmarkItem($existing),
                'bookmarked' => true,
            ]);
        }

        if (!$bookmark->add((int) $user['id'], $repoId)) {
            $this->fail('Gagal menambahkan bookmark.', 500);
        }

        $created = $bookmark->findByRepositoryForUser((int) $user['id'], $repoId);
        $this->success([
            'item'       => $this->normalizeBookmarkItem($created),
            'bookmarked' => true,
        ], 201);
    }

    public function destroy($id): void
    {
        $user = $this->requireUser();
        $bookmarkId = (int) $id;
        if ($bookmarkId <= 0) {
            $this->fail('ID bookmark tidak valid.', 400);
        }

        $bookmark = new Bookmark();
        $item = $bookmark->findByIdForUser((int) $user['id'], $bookmarkId);
        if (!$item) {
            $this->fail('Bookmark tidak ditemukan.', 404);
        }

        if (!$bookmark->removeByBookmarkId((int) $user['id'], $bookmarkId)) {
            $this->fail('Gagal menghapus bookmark.', 500);
        }

        $this->success([
            'message'    => 'Bookmark dihapus.',
            'item'       => $this->normalizeBookmarkItem($item),
            'bookmarked' => false,
        ]);
    }

    public function toggle(): void
    {
        $user    = $this->requireUser();
        $payload = array_merge($this->getJsonInput(), $_POST ?? []);

        $repoModel = new Repository();
        $repoId = $this->resolveRepositoryId($payload, $repoModel);
        $repo = $repoModel->findById($repoId);
        if (!$repo) {
            $this->fail('Repository tidak ditemukan.', 404);
        }

        if (!$repoModel->isPublicAccessible($repo)) {
            $this->fail('Repository tidak tersedia untuk dibookmark.', 403);
        }

        $bookmark = new Bookmark();
        $bookmarked = $bookmark->toggle((int) $user['id'], $repoId);
        $item = $bookmark->findByRepositoryForUser((int) $user['id'], $repoId);

        $this->success([
            'bookmarked' => $bookmarked,
            'item'       => $this->normalizeBookmarkItem($item),
        ]);
    }

    private function resolveRepositoryId(array $payload, Repository $repoModel): int
    {
        $repoId = isset($payload['repository_id']) ? (int) $payload['repository_id'] : null;
        $slug = $this->sanitizeString($payload['slug'] ?? '', 200);

        if (($repoId === null || $repoId <= 0) && $slug !== '') {
            $repo = $repoModel->getBySlug($slug);
            if (!$repo) {
                $this->fail('Repository tidak ditemukan.', 404);
            }
            $repoId = (int) $repo['id'];
        }

        if (!$repoId || $repoId <= 0) {
            $this->fail('repository_id atau slug wajib diisi.', 422);
        }

        return $repoId;
    }

    private function normalizeBookmarkItems(array $items): array
    {
        return array_map(function ($item) {
            return $this->normalizeBookmarkItem($item);
        }, $items);
    }

    private function normalizeBookmarkItem(?array $item): ?array
    {
        if (!$item) {
            return null;
        }

        $repoId = (int) ($item['repository_id'] ?? 0);
        if ($repoId > 0) {
            $base = rtrim((string) ($this->config['base_url'] ?? ''), '/');
            $item['id'] = $repoId; // konsisten dengan endpoint detail repository berbasis id
            $item['identifier'] = (string) $repoId;
            $item['identifier_type'] = 'id';
            $item['detail_url'] = $base . '/api/mobile/public/repositories/' . $repoId;
        }

        return $item;
    }
}
