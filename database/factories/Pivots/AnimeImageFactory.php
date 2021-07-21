<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\AnimeImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeImageFactory.
 *
 * @method AnimeImage createOne($attributes = [])
 * @method AnimeImage makeOne($attributes = [])
 */
class AnimeImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AnimeImage::class;

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
