<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeImageFactory.
 *
 * @method AnimeImage createOne($attributes = [])
 * @method AnimeImage makeOne($attributes = [])
 *
 * @extends Factory<AnimeImage>
 */
class AnimeImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeImage>
     */
    protected $model = AnimeImage::class;

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
