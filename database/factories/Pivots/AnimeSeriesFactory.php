<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeSeriesFactory.
 */
class AnimeSeriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AnimeSeries::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
