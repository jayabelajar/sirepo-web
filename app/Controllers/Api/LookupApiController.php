<?php

namespace App\Controllers\Api;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Repository;

class LookupApiController extends BaseApiController
{
    public function index(): void
    {
        $this->success($this->buildLookupPayload());
    }

    public function categories(): void
    {
        $payload = $this->buildLookupPayload();
        $this->success([
            'categories' => [
                'program_studi' => $payload['program_studi'],
                'mata_kuliah'   => $payload['mata_kuliah'],
                'years'         => $payload['years'],
                'jenis_karya'   => $payload['jenis_karya'],
                'authors'       => $payload['authors'],
            ],
        ]);
    }

    public function categoryPage(): void
    {
        $payload = $this->buildLookupPayload();

        $groups = [
            [
                'type'  => 'program_studi',
                'title' => 'Program Studi',
                'items' => array_map(static function (array $row): array {
                    return [
                        'value' => (string) ($row['id'] ?? ''),
                        'label' => (string) ($row['nama_program_studi'] ?? '-'),
                        'total' => (int) ($row['total'] ?? 0),
                    ];
                }, $payload['program_studi']),
            ],
            [
                'type'  => 'mata_kuliah',
                'title' => 'Mata Kuliah',
                'items' => array_map(static function (array $row): array {
                    return [
                        'value' => (string) ($row['id'] ?? ''),
                        'label' => (string) ($row['nama'] ?? '-'),
                        'total' => (int) ($row['total'] ?? 0),
                    ];
                }, $payload['mata_kuliah']),
            ],
            [
                'type'  => 'tahun',
                'title' => 'Tahun',
                'items' => array_map(static function (array $row): array {
                    $label = (string) ($row['label'] ?? '');
                    return [
                        'value' => $label,
                        'label' => $label,
                        'total' => (int) ($row['total'] ?? 0),
                    ];
                }, $payload['years']),
            ],
            [
                'type'  => 'jenis_karya',
                'title' => 'Jenis Karya',
                'items' => array_map(static function (array $row): array {
                    $label = (string) ($row['label'] ?? '');
                    return [
                        'value' => $label,
                        'label' => $label,
                        'total' => (int) ($row['total'] ?? 0),
                    ];
                }, $payload['jenis_karya']),
            ],
            [
                'type'  => 'author',
                'title' => 'Author',
                'items' => array_map(static function (array $row): array {
                    $label = (string) ($row['label'] ?? '');
                    return [
                        'value' => $label,
                        'label' => $label,
                        'total' => (int) ($row['total'] ?? 0),
                    ];
                }, $payload['authors']),
            ],
        ];

        $this->success([
            'groups' => $groups,
        ]);
    }

    private function buildLookupPayload(): array
    {
        $repo  = new Repository();
        $prodi = new ProgramStudi();
        $mk    = new MataKuliah();

        return [
            'program_studi' => $prodi->getAllWithCount(),
            'mata_kuliah'   => $mk->getAll(),
            'years'         => $repo->getAvailableYears(),
            'jenis_karya'   => $repo->getJenisKaryaList(),
            'authors'       => $repo->getAuthors(),
        ];
    }
}
