<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Buku::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => $this->faker->sentence(3),
            'deskripsi' => $this->faker->paragraph,
            'sinopsis' => $this->faker->paragraph,
            'ringkasan' => $this->faker->sentence(5),
            'tahun_terbit' => $this->faker->numberBetween(1900, date('Y')),
            'isbn' => $this->faker->isbn13,
            'stok' => $this->faker->numberBetween(1, 20),
            'stok_tersedia' => $this->faker->numberBetween(0, 20),
            'kategori_id' => Kategori::factory(),
            'penulis_id' => Penulis::factory(),
            'penerbit_id' => Penerbit::factory(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function withSampul(string $filename)
    {
        return $this->state(function (array $attributes) use ($filename) {
            return [
                'sampul' => $filename,
            ];
        });
    }
}
