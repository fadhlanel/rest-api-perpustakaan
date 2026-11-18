<?php

namespace Database\Factories;

use App\Models\Penulis;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenulisFactory extends Factory
{
    protected $model = Penulis::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'biografi' => $this->faker->paragraph(),
        ];
    }
}
