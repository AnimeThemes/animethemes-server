<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class ImageTest.
 */
class ImageTest extends TestCase
{
    use WithFaker;

    /**
     * The facet attribute of an image shall be cast to an ImageFacet enum instance.
     *
     * @return void
     */
    public function testCastsFacetToEnum()
    {
        $image = Image::factory()->createOne();

        $facet = $image->facet;

        static::assertInstanceOf(ImageFacet::class, $facet);
    }

    /**
     * Images shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $image = Image::factory()->createOne();

        static::assertEquals(1, $image->audits()->count());
    }

    /**
     * Images shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $image = Image::factory()->createOne();

        static::assertIsString($image->getName());
    }

    /**
     * Image shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
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
     *
     * @return void
     */
    public function testArtists()
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
     * The image shall not be deleted from storage when the Image is deleted.
     *
     * @return void
     */
    public function testImageStorageDeletion()
    {
        $fs = Storage::fake('images');
        $file = File::fake()->image($this->faker->word().'.jpg');
        $fsFile = $fs->putFile('', $file);
        $fsPathinfo = pathinfo(strval($fsFile));

        $image = Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => ImageFacet::getRandomValue(),
            Image::ATTRIBUTE_MIMETYPE => MimeType::from($fsPathinfo['basename']),
            Image::ATTRIBUTE_PATH => $fsFile,
            Image::ATTRIBUTE_SIZE => $this->faker->randomNumber(),
        ]);

        $image->delete();

        static::assertTrue($fs->exists($image->path));
    }

    /**
     * The image shall be deleted from storage when the Image is force deleted.
     *
     * @return void
     */
    public function testImageStorageForceDeletion()
    {
        $fs = Storage::fake('images');
        $file = File::fake()->image($this->faker->word().'.jpg');
        $fsFile = $fs->putFile('', $file);
        $fsPathinfo = pathinfo(strval($fsFile));

        $image = Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => ImageFacet::getRandomValue(),
            Image::ATTRIBUTE_MIMETYPE => MimeType::from($fsPathinfo['basename']),
            Image::ATTRIBUTE_PATH => $fsFile,
            Image::ATTRIBUTE_SIZE => $this->faker->randomNumber(),
        ]);

        $image->forceDelete();

        static::assertFalse($fs->exists($image->path));
    }
}
