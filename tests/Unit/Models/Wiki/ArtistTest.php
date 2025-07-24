<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use WithFaker;

    /**
     * Artist shall be a searchable resource.
     */
    public function testSearchableAs(): void
    {
        $artist = Artist::factory()->createOne();

        static::assertIsString($artist->searchableAs());
    }

    /**
     * Artist shall be a searchable resource.
     */
    public function testToSearchableArray(): void
    {
        $artist = Artist::factory()->createOne();

        static::assertIsArray($artist->toSearchableArray());
    }

    /**
     * Artists shall be nameable.
     */
    public function testNameable(): void
    {
        $artist = Artist::factory()->createOne();

        static::assertIsString($artist->getName());
    }

    /**
     * Artists shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $artist = Artist::factory()->createOne();

        static::assertIsString($artist->getSubtitle());
    }

    /**
     * Artist shall have a many-to-many relationship with the type Song.
     */
    public function testSongs(): void
    {
        $songCount = $this->faker->randomDigitNotNull();

        $artist = Artist::factory()
            ->has(Song::factory()->count($songCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $artist->songs());
        static::assertEquals($songCount, $artist->songs()->count());
        static::assertInstanceOf(Song::class, $artist->songs()->first());
        static::assertEquals(ArtistSong::class, $artist->songs()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship with the type ExternalResource.
     */
    public function testExternalResources(): void
    {
        $resourceCount = $this->faker->randomDigitNotNull();

        $artist = Artist::factory()
            ->has(ExternalResource::factory()->count($resourceCount), 'resources')
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $artist->resources());
        static::assertEquals($resourceCount, $artist->resources()->count());
        static::assertInstanceOf(ExternalResource::class, $artist->resources()->first());
        static::assertEquals(ArtistResource::class, $artist->resources()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship to the type Artist as "members".
     */
    public function testMembers(): void
    {
        $memberCount = $this->faker->randomDigitNotNull();

        $artist = Artist::factory()
            ->has(Artist::factory()->count($memberCount), 'members')
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $artist->members());
        static::assertEquals($memberCount, $artist->members()->count());
        static::assertInstanceOf(Artist::class, $artist->members()->first());
        static::assertEquals(ArtistMember::class, $artist->members()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship to the type Artist as "groups".
     */
    public function testGroups(): void
    {
        $groupCount = $this->faker->randomDigitNotNull();

        $artist = Artist::factory()
            ->has(Artist::factory()->count($groupCount), 'groups')
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $artist->groups());
        static::assertEquals($groupCount, $artist->groups()->count());
        static::assertInstanceOf(Artist::class, $artist->groups()->first());
        static::assertEquals(ArtistMember::class, $artist->groups()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship with the type Image.
     */
    public function testImages(): void
    {
        $imageCount = $this->faker->randomDigitNotNull();

        $artist = Artist::factory()
            ->has(Image::factory()->count($imageCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $artist->images());
        static::assertEquals($imageCount, $artist->images()->count());
        static::assertInstanceOf(Image::class, $artist->images()->first());
        static::assertEquals(ArtistImage::class, $artist->images()->getPivotClass());
    }
}
