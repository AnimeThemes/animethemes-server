<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistSongFactory.
 *
 * @method ArtistSong createOne($attributes = [])
 * @method ArtistSong makeOne($attributes = [])
 */
class ArtistSongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArtistSong::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            ArtistSong::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
