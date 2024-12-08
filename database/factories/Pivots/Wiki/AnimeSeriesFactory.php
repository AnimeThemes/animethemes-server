<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeSeriesFactory.
 *
 * @method AnimeSeries createOne($attributes = [])
 * @method AnimeSeries makeOne($attributes = [])
 *
 * @extends Factory<AnimeSeries>
 */
class AnimeSeriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeSeries>
     */
    protected $model = AnimeSeries::class;

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
