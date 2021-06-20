<?php

declare(strict_types=1);

namespace Models\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ArtistTest.
 */
class ArtistTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Artist shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $artist = Artist::factory()->create();

        static::assertIsString($artist->searchableAs());
    }

    /**
     * Artist shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $artist = Artist::factory()->create();

        static::assertIsArray($artist->toSearchableArray());
    }

    /**
     * Artists shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $artist = Artist::factory()->create();

        static::assertEquals(1, $artist->audits->count());
    }

    /**
     * Artists shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $artist = Artist::factory()->create();

        static::assertIsString($artist->getName());
    }

    /**
     * Artist shall have a many-to-many relationship with the type Song.
     *
     * @return void
     */
    public function testSongs()
    {
        $songCount = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Song::factory()->count($songCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $artist->songs());
        static::assertEquals($songCount, $artist->songs()->count());
        static::assertInstanceOf(Song::class, $artist->songs()->first());
        static::assertEquals(ArtistSong::class, $artist->songs()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship with the type ExternalResource.
     *
     * @return void
     */
    public function testExternalResources()
    {
        $resourceCount = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(ExternalResource::factory()->count($resourceCount), 'resources')
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $artist->resources());
        static::assertEquals($resourceCount, $artist->resources()->count());
        static::assertInstanceOf(ExternalResource::class, $artist->resources()->first());
        static::assertEquals(ArtistResource::class, $artist->resources()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship to the type Artist as "members".
     *
     * @return void
     */
    public function testMembers()
    {
        $memberCount = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Artist::factory()->count($memberCount), 'members')
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $artist->members());
        static::assertEquals($memberCount, $artist->members()->count());
        static::assertInstanceOf(Artist::class, $artist->members()->first());
        static::assertEquals(ArtistMember::class, $artist->members()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship to the type Artist as "groups".
     *
     * @return void
     */
    public function testGroups()
    {
        $groupCount = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Artist::factory()->count($groupCount), 'groups')
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $artist->groups());
        static::assertEquals($groupCount, $artist->groups()->count());
        static::assertInstanceOf(Artist::class, $artist->groups()->first());
        static::assertEquals(ArtistMember::class, $artist->groups()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship with the type Image.
     *
     * @return void
     */
    public function testImages()
    {
        $imageCount = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Image::factory()->count($imageCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $artist->images());
        static::assertEquals($imageCount, $artist->images()->count());
        static::assertInstanceOf(Image::class, $artist->images()->first());
        static::assertEquals(ArtistImage::class, $artist->images()->getPivotClass());
    }
}
