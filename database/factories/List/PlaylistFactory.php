<?php

declare(strict_types=1);

namespace Database\Factories\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class PlaylistFactory.
 *
 * @method Playlist createOne($attributes = [])
 * @method Playlist makeOne($attributes = [])
 *
 * @extends Factory<Playlist>
 */
class PlaylistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Playlist>
     */
    protected $model = Playlist::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visibility = Arr::random(PlaylistVisibility::cases());

        return [
            Playlist::ATTRIBUTE_NAME => fake()->words(3, true),
            Playlist::ATTRIBUTE_DESCRIPTION => fake()->words(10, true),
            Playlist::ATTRIBUTE_VISIBILITY => $visibility->value,
        ];
    }

    /**
     * Define the model's track listing.
     *
     * @param  int  $count
     * @return static
     */
    public function tracks(int $count): static
    {
        return $this->afterCreating(
            function (Playlist $playlist) use ($count) {
                $tracks = [];

                foreach (range(1, $count) as $index) {
                    /** @var PlaylistTrack|null $last */
                    $last = Arr::last($tracks);

                    $entryVideo = AnimeThemeEntryVideo::factory()
                        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                        ->for(Video::factory())
                        ->createOne();

                    $track = PlaylistTrack::factory()
                        ->for($playlist)
                        ->for($entryVideo->video)
                        ->for($entryVideo->animethemeentry)
                        ->createOne();

                    if ($index === 1) {
                        $playlist->first()->associate($track)->save();
                    }

                    if ($last !== null) {
                        $last->next()->associate($track);
                        $last->save();

                        $track->previous()->associate($last);
                        $track->save();
                    }

                    if ($index === $count) {
                        $playlist->last()->associate($track);
                        $playlist->save();
                    }

                    $tracks[] = $track;
                }
            }
        );
    }

    /**
     * Define the model's track listing.
     *
     * @param  array  $videoIds
     * @return static
     */
    public function tracksForIds(array $videoIds): static
    {
        return $this->afterCreating(
            function (Playlist $playlist) use ($videoIds) {
                $tracks = [];

                for ($index = 0; $index < count($videoIds); $index++) {
                    $videoId = $videoIds[$index];

                    /** @var PlaylistTrack|null $last */
                    $last = Arr::last($tracks);

                    $track = PlaylistTrack::factory()
                        ->for($playlist)
                        ->for(Video::query()->find($videoId))
                        ->for(Video::query()->find($videoId)->animethemeentries()->first())
                        ->createOne();

                    if ($index === 1) {
                        $playlist->first()->associate($track)->save();
                    }

                    if ($last !== null) {
                        $last->next()->associate($track);
                        $last->save();

                        $track->previous()->associate($last);
                        $track->save();
                    }

                    if ($index === count($videoIds)) {
                        $playlist->last()->associate($track);
                        $playlist->save();
                    }

                    $tracks[] = $track;
                }
            }
        );
    }
}
