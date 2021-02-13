<?php

namespace Tests\Unit\Models;

use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Artist shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $artist = Artist::factory()->create();

        $this->assertIsString($artist->searchableAs());
    }

    /**
     * Artist shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $artist = Artist::factory()->create();

        $this->assertIsArray($artist->toSearchableArray());
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

        $this->assertEquals(1, $artist->audits->count());
    }

    /**
     * Artists shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $artist = Artist::factory()->create();

        $this->assertIsString($artist->getName());
    }

    /**
     * Artist shall have a many-to-many relationship with the type Song.
     *
     * @return void
     */
    public function testSongs()
    {
        $song_count = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Song::factory()->count($song_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $artist->songs());
        $this->assertEquals($song_count, $artist->songs()->count());
        $this->assertInstanceOf(Song::class, $artist->songs()->first());
        $this->assertEquals(ArtistSong::class, $artist->songs()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship with the type ExternalResource.
     *
     * @return void
     */
    public function testExternalResources()
    {
        $resource_count = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(ExternalResource::factory()->count($resource_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $artist->externalResources());
        $this->assertEquals($resource_count, $artist->externalResources()->count());
        $this->assertInstanceOf(ExternalResource::class, $artist->externalResources()->first());
        $this->assertEquals(ArtistResource::class, $artist->externalResources()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship to the type Artist as "members".
     *
     * @return void
     */
    public function testMembers()
    {
        $member_count = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Artist::factory()->count($member_count), 'members')
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $artist->members());
        $this->assertEquals($member_count, $artist->members()->count());
        $this->assertInstanceOf(Artist::class, $artist->members()->first());
        $this->assertEquals(ArtistMember::class, $artist->members()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship to the type Artist as "groups".
     *
     * @return void
     */
    public function testGroups()
    {
        $group_count = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Artist::factory()->count($group_count), 'groups')
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $artist->groups());
        $this->assertEquals($group_count, $artist->groups()->count());
        $this->assertInstanceOf(Artist::class, $artist->groups()->first());
        $this->assertEquals(ArtistMember::class, $artist->groups()->getPivotClass());
    }

    /**
     * Artist shall have a many-to-many relationship with the type Image.
     *
     * @return void
     */
    public function testImages()
    {
        $image_count = $this->faker->randomDigitNotNull;

        $artist = Artist::factory()
            ->has(Image::factory()->count($image_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $artist->images());
        $this->assertEquals($image_count, $artist->images()->count());
        $this->assertInstanceOf(Image::class, $artist->images()->first());
        $this->assertEquals(ArtistImage::class, $artist->images()->getPivotClass());
    }
}
