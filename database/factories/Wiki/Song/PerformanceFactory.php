<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Song;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Performance createOne($attributes = [])
 * @method Performance makeOne($attributes = [])
 *
 * @extends Factory<Performance>
 */
#[UseModel(Performance::class)]
class PerformanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Performance::ATTRIBUTE_ALIAS => fake()->text(),
            Performance::ATTRIBUTE_AS => fake()->text(),
            Performance::ATTRIBUTE_ARTIST => Artist::factory(),
            Performance::ATTRIBUTE_SONG => Song::factory(),
        ];
    }
}
