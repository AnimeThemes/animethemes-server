<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method AnimeSeries createOne($attributes = [])
 * @method AnimeSeries makeOne($attributes = [])
 *
 * @extends Factory<AnimeSeries>
 */
#[UseModel(AnimeSeries::class)]
class AnimeSeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
