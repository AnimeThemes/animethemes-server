<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method ArtistSong createOne($attributes = [])
 * @method ArtistSong makeOne($attributes = [])
 *
 * @extends Factory<ArtistSong>
 */
#[UseModel(ArtistSong::class)]
class ArtistSongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            ArtistSong::ATTRIBUTE_ALIAS => Str::random(),
            ArtistSong::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
