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
 */
class AnimeSynonymFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
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
            'text' => $this->faker->words(3, true),
        ];
    }
}
