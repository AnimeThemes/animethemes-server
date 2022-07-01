<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SynonymFactory.
 *
 * @method AnimeSynonym createOne($attributes = [])
 * @method AnimeSynonym makeOne($attributes = [])
 *
 * @extends Factory<AnimeSynonym>
 */
class AnimeSynonymFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeSynonym>
     */
    protected $model = AnimeSynonym::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            AnimeSynonym::ATTRIBUTE_TEXT => fake()->words(3, true),
        ];
    }
}
