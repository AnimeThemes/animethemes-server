<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Constants\FeatureConstants;
use App\Models\Admin\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Feature createOne($attributes = [])
 * @method Feature makeOne($attributes = [])
 *
 * @extends Factory<Feature>
 */
class FeatureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Feature>
     */
    protected $model = Feature::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Feature::ATTRIBUTE_NAME => fake()->unique()->word(),
            Feature::ATTRIBUTE_SCOPE => FeatureConstants::NULL_SCOPE,
            Feature::ATTRIBUTE_VALUE => fake()->boolean(),
        ];
    }
}
