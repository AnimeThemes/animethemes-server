<?php

declare(strict_types=1);

namespace Tests\Unit\Models\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PlaylistTest extends TestCase
{
    use WithFaker;

    /**
     * The visibility attribute of a playlist shall be cast to a PlaylistVisibility enum instance.
     */
    public function testCastsSeasonToEnum(): void
    {
        $playlist = Playlist::factory()->createOne();

        $visibility = $playlist->visibility;

        static::assertInstanceOf(PlaylistVisibility::class, $visibility);
    }

    /**
     * Playlist shall be nameable.
     */
    public function testNameable(): void
    {
        $playlist = Playlist::factory()->createOne();

        static::assertIsString($playlist->getName());
    }

    /**
     * Playlist shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        static::assertIsString($playlist->getSubtitle());
    }

    /**
     * Public playlists shall be searchable.
     */
    public function testSearchableIfPublic(): void
    {
        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        static::assertTrue($playlist->shouldBeSearchable());
    }

    /**
     * Playlists shall not be searchable if not public.
     */
    public function testNotSearchableIfNotPublic(): void
    {
        $visibility = null;

        while ($visibility == null) {
            $candidate = Arr::random(PlaylistVisibility::cases());
            if ($candidate !== PlaylistVisibility::PUBLIC) {
                $visibility = $candidate;
            }
        }

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => $visibility->value,
            ]);

        static::assertFalse($playlist->shouldBeSearchable());
    }

    /**
     * Playlists shall filter null user_id values from hashids.
     */
    public function testHashidsNullableUser(): void
    {
        $playlist = Playlist::factory()->createOne();

        static::assertEmpty(array_diff([$playlist->playlist_id], $playlist->hashids()));
        static::assertEmpty(array_diff($playlist->hashids(), [$playlist->playlist_id]));
    }

    /**
     * Playlists shall include nonnull user_id values from hashids.
     */
    public function testHashidsNonNullUser(): void
    {
        $user = User::factory()->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        static::assertEmpty(array_diff([$user->id, $playlist->playlist_id], $playlist->hashids()));
        static::assertEmpty(array_diff($playlist->hashids(), [$user->id, $playlist->playlist_id]));
    }

    /**
     * Playlists shall belong to a User.
     */
    public function testUser(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $playlist->user());
        static::assertInstanceOf(User::class, $playlist->user()->first());
    }

    /**
     * Playlists shall link to the first Track.
     */
    public function testFirst(): void
    {
        $playlist = Playlist::factory()
            ->createOne();

        $first = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $playlist->first()->associate($first)->save();

        static::assertInstanceOf(BelongsTo::class, $playlist->first());
        static::assertInstanceOf(PlaylistTrack::class, $playlist->first()->first());
    }

    /**
     * Playlists shall link to the last Track.
     */
    public function testLast(): void
    {
        $playlist = Playlist::factory()->createOne();

        $last = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $playlist->last()->associate($last)->save();

        static::assertInstanceOf(BelongsTo::class, $playlist->last());
        static::assertInstanceOf(PlaylistTrack::class, $playlist->last()->first());
    }

    /**
     * Playlists shall have a many-to-many relationship with the type Image.
     */
    public function testImages(): void
    {
        $imageCount = $this->faker->randomDigitNotNull();

        $playlist = Playlist::factory()
            ->has(Image::factory()->count($imageCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $playlist->images());
        static::assertEquals($imageCount, $playlist->images()->count());
        static::assertInstanceOf(Image::class, $playlist->images()->first());
        static::assertEquals(PlaylistImage::class, $playlist->images()->getPivotClass());
    }

    /**
     * Playlists shall have a one-to-many relationship with the type PlaylistTrack.
     */
    public function testTracks(): void
    {
        $trackCount = $this->faker->randomDigitNotNull();

        $playlist = Playlist::factory()->createOne();

        PlaylistTrack::factory()
            ->for($playlist)
            ->count($trackCount)
            ->create();

        static::assertInstanceOf(HasMany::class, $playlist->tracks());
        static::assertEquals($trackCount, $playlist->tracks()->count());
        static::assertInstanceOf(PlaylistTrack::class, $playlist->tracks()->first());
    }
}
