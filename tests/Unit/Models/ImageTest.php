<?php

namespace Tests\Unit\Models;

use App\Enums\ImageFacet;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use App\Pivots\AnimeImage;
use App\Pivots\ArtistImage;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The facet attribute of an image shall be cast to an ImageFacet enum instance.
     *
     * @return void
     */
    public function testCastsFacetToEnum()
    {
        $image = Image::factory()->create();

        $facet = $image->facet;

        $this->assertInstanceOf(ImageFacet::class, $facet);
    }

    /**
     * Images shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $image = Image::factory()->create();

        $this->assertEquals(1, $image->audits->count());
    }

    /**
     * Images shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $image = Image::factory()->create();

        $this->assertIsString($image->getName());
    }

    /**
     * Image shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $anime_count = $this->faker->randomDigitNotNull;

        $image = Image::factory()
            ->has(Anime::factory()->count($anime_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $image->anime());
        $this->assertEquals($anime_count, $image->anime()->count());
        $this->assertInstanceOf(Anime::class, $image->anime()->first());
        $this->assertEquals(AnimeImage::class, $image->anime()->getPivotClass());
    }

    /**
     * Image shall have a many-to-many relationship with the type Artist.
     *
     * @return void
     */
    public function testArtists()
    {
        $artist_count = $this->faker->randomDigitNotNull;

        $image = Image::factory()
            ->has(Artist::factory()->count($artist_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $image->artists());
        $this->assertEquals($artist_count, $image->artists()->count());
        $this->assertInstanceOf(Artist::class, $image->artists()->first());
        $this->assertEquals(ArtistImage::class, $image->artists()->getPivotClass());
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
        $fs_file = $fs->put('', $file);
        $fs_pathinfo = pathinfo(strval($fs_file));

        $image = Image::create([
            'path' => $fs_file,
            'facet' => ImageFacet::getRandomValue(),
            'size' => $this->faker->randomNumber(),
            'mimetype' => MimeType::fromFilename($fs_pathinfo['basename']),
        ]);

        $image->delete();

        $this->assertTrue($fs->exists($image->path));
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
        $fs_file = $fs->put('', $file);
        $fs_pathinfo = pathinfo(strval($fs_file));

        $image = Image::create([
            'path' => $fs_file,
            'facet' => ImageFacet::getRandomValue(),
            'size' => $this->faker->randomNumber(),
            'mimetype' => MimeType::fromFilename($fs_pathinfo['basename']),
        ]);

        $image->forceDelete();

        $this->assertFalse($fs->exists($image->path));
    }
}
