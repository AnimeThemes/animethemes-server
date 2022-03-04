<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class SeriesFactory.
 *
 * @method Series createOne($attributes = [])
 * @method Series makeOne($attributes = [])
 *
 * @extends Factory<Series>
 */
class SeriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Series>
     */
    protected $model = Series::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Series::ATTRIBUTE_NAME => $this->faker->words(3, true),
            Series::ATTRIBUTE_SLUG => Str::slug($this->faker->text(), '_'),
        ];
    }
}
