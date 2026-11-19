<?php

namespace App\Support;

use App\Models\Buku;
use Illuminate\Support\Collection;

class MasterDataIssues
{
    /**
     * Daftar master data yang masih memiliki buku tanpa relasi.
     *
     * @return Collection<int, array{type: string, count: int, message: string, url: string}>
     */
    public static function all(): Collection
    {
        $labels = [
            'kategori' => 'kategorinya',
            'penulis' => 'penulisnya',
            'penerbit' => 'penerbitnya',
        ];

        return collect(array_keys($labels))
            ->map(function (string $type) use ($labels): array {
                $count = Buku::whereNull($type.'_id')->count();

                return [
                    'type' => $type,
                    'count' => $count,
                    'message' => "{$count} Buku data {$labels[$type]} kosong, perbaiki datanya segera!",
                    'url' => route('admin.master.reassign', $type),
                ];
            })
            ->filter(fn (array $issue): bool => $issue['count'] > 0)
            ->values();
    }

    public static function total(): int
    {
        return self::all()->sum('count');
    }
}
