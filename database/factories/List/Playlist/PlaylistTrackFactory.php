<?php

declare(strict_types=1);

namespace Database\Factories\List\Playlist;

use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
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
        return [];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (PlaylistTrack $track): void {
            if ($track->playlist_id === null) {
                return;
            }

            $position = PlaylistTrack::query()
                ->whereBelongsTo($track->playlist)
                ->max(PlaylistTrack::ATTRIBUTE_POSITION) ?? 0;

            $track->update([PlaylistTrack::ATTRIBUTE_POSITION => $position + 1]);
        });
    }
}
