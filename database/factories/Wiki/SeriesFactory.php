<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Series createOne($attributes = [])
 * @method Series makeOne($attributes = [])
 *
 * @extends Factory<Series>
 */
#[UseModel(Series::class)]
class SeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Series::ATTRIBUTE_NAME => fake()->words(3, true),
            Series::ATTRIBUTE_SLUG => Str::slug(fake()->text(191), '_'),
        ];
    }
}
