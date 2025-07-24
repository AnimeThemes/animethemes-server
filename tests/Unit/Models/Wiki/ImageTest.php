<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageForceDeleting;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use WithFaker;

    /**
     * The facet attribute of an image shall be cast to an ImageFacet enum instance.
     */
    public function testCastsFacetToEnum(): void
    {
        $image = Image::factory()->createOne();

        $facet = $image->facet;

        static::assertInstanceOf(ImageFacet::class, $facet);
    }

    /**
     * Images shall be nameable.
     */
    public function testNameable(): void
    {
        $image = Image::factory()->createOne();

        static::assertIsString($image->getName());
    }

    /**
     * Images shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $image = Image::factory()->createOne();

        static::assertIsString($image->getSubtitle());
    }

    /**
     * Image shall have a many-to-many relationship with the type Anime.
     */
    public function testAnime(): void
    {
        $animeCount = $this->faker->randomDigitNotNull();

        $image = Image::factory()
            ->has(Anime::factory()->count($animeCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $image->anime());
        static::assertEquals($animeCount, $image->anime()->count());
        static::assertInstanceOf(Anime::class, $image->anime()->first());
        static::assertEquals(AnimeImage::class, $image->anime()->getPivotClass());
    }

    /**
     * Image shall have a many-to-many relationship with the type Artist.
     */
    public function testArtists(): void
    {
        $artistCount = $this->faker->randomDigitNotNull();

        $image = Image::factory()
            ->has(Artist::factory()->count($artistCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $image->artists());
        static::assertEquals($artistCount, $image->artists()->count());
        static::assertInstanceOf(Artist::class, $image->artists()->first());
        static::assertEquals(ArtistImage::class, $image->artists()->getPivotClass());
    }

    /**
     * Image shall have a many-to-many relationship with the type Studio.
     */
    public function testStudios(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $image = Image::factory()
            ->has(Studio::factory()->count($studioCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $image->studios());
        static::assertEquals($studioCount, $image->studios()->count());
        static::assertInstanceOf(Studio::class, $image->studios()->first());
        static::assertEquals(StudioImage::class, $image->studios()->getPivotClass());
    }

    /**
     * Image shall have a many-to-many relationship with the type Playlist.
     */
    public function testPlaylists(): void
    {
        $playlistCount = $this->faker->randomDigitNotNull();

        $image = Image::factory()
            ->has(Playlist::factory()->count($playlistCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $image->playlists());
        static::assertEquals($playlistCount, $image->playlists()->count());
        static::assertInstanceOf(Playlist::class, $image->playlists()->first());
    }

    /**
     * The image shall not be deleted from storage when the Image is deleted.
     */
    public function testImageStorageDeletion(): void
    {
        $fs = Storage::fake(Config::get('image.disk'));
        $file = File::fake()->image($this->faker->word().'.jpg');
        $fsFile = $fs->putFile('', $file);

        $facet = Arr::random(ImageFacet::cases());

        $image = Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        $image->delete();

        static::assertTrue($fs->exists($image->path));
    }

    /**
     * The image shall be deleted from storage when the Image is force deleted.
     */
    public function testImageStorageForceDeletion(): void
    {
        Event::fakeExcept(ImageForceDeleting::class);

        $fs = Storage::fake(Config::get('image.disk'));
        $file = File::fake()->image($this->faker->word().'.jpg');
        $fsFile = $fs->putFile('', $file);

        $facet = Arr::random(ImageFacet::cases());

        $image = Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        $image->forceDelete();

        static::assertFalse($fs->exists($image->path));
    }
}
