<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\AnimeResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class AnimeResourceFactory.
 *
 * @method AnimeResource createOne($attributes = [])
 * @method AnimeResource makeOne($attributes = [])
 */
class AnimeResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AnimeResource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            AnimeResource::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
