<?php

namespace Database\Factories;

use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ends_at' => Carbon::now()->addDays(rand(1, 10)),
            'starts_at' => Carbon::now(),
        ];
    }
}
