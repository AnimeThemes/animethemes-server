<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
        $type = Arr::random(AnimeSynonymType::cases());

        return [
            AnimeSynonym::ATTRIBUTE_TEXT => fake()->words(3, true),
            AnimeSynonym::ATTRIBUTE_TYPE => $type->value,
        ];
    }
}
