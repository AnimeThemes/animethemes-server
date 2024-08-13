<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class AnimeResourceFactory.
 *
 * @method AnimeResource createOne($attributes = [])
 * @method AnimeResource makeOne($attributes = [])
 *
 * @extends Factory<AnimeResource>
 */
class AnimeResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeResource>
     */
    protected $model = AnimeResource::class;

    /**
     * Define the model's default state.
     *
     * @phpstan-ignore-next-line
     * @return array
     */
    public function definition(): array
    {
        return [
            AnimeResource::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
