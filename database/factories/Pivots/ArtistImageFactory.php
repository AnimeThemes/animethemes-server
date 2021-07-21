<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\ArtistImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ArtistImageFactory.
 *
 * @method ArtistImage createOne($attributes = [])
 * @method ArtistImage makeOne($attributes = [])
 */
class ArtistImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArtistImage::class;

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
