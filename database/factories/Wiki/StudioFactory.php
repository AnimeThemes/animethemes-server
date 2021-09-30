<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class SeriesFactory.
 *
 * @method Studio createOne($attributes = [])
 * @method Studio makeOne($attributes = [])
 */
class StudioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Studio::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Studio::ATTRIBUTE_NAME => $this->faker->words(3, true),
            Studio::ATTRIBUTE_SLUG => Str::slug($this->faker->text(), '_'),
        ];
    }
}
