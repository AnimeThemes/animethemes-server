<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistSongFactory.
 *
 * @method ArtistSong createOne($attributes = [])
 * @method ArtistSong makeOne($attributes = [])
 *
 * @extends Factory<ArtistSong>
 */
class ArtistSongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ArtistSong>
     */
    protected $model = ArtistSong::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            ArtistSong::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
