<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ulasan>
 */
class UlasanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peminjaman_id' => Peminjaman::factory(),
            'buku_id' => Buku::factory(),
            'nis' => fake()->numerify('##########'),
            'rating' => fake()->numberBetween(1, 5),
            'komentar' => fake()->sentence(),
        ];
    }

    /**
     * Ulasan tanpa komentar (komentar opsional).
     */
    public function tanpaKomentar(): static
    {
        return $this->state(fn () => ['komentar' => null]);
    }
}
