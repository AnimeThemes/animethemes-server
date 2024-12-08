<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\List;

use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeImageFactory.
 *
 * @method PlaylistImage createOne($attributes = [])
 * @method PlaylistImage makeOne($attributes = [])
 *
 * @extends Factory<PlaylistImage>
 */
class PlaylistImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<PlaylistImage>
     */
    protected $model = PlaylistImage::class;

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
