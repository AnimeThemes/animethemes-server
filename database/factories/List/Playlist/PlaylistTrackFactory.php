<?php

declare(strict_types=1);

namespace Database\Factories\List\Playlist;

use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TrackFactory.
 *
 * @method PlaylistTrack createOne($attributes = [])
 * @method PlaylistTrack makeOne($attributes = [])
 *
 * @extends Factory<PlaylistTrack>
 */
class PlaylistTrackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<PlaylistTrack>
     */
    protected $model = PlaylistTrack::class;

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
