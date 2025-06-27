<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\List;

use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class PlaylistImageTest.
 */
class PlaylistImageTest extends TestCase
{
    /**
     * An PlaylistImage shall belong to a Playlist.
     *
     * @return void
     */
    public function test_playlist(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $playlistImage->playlist());
        static::assertInstanceOf(Playlist::class, $playlistImage->playlist()->first());
    }

    /**
     * An PlaylistImage shall belong to an Image.
     *
     * @return void
     */
    public function test_image(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $playlistImage->image());
        static::assertInstanceOf(Image::class, $playlistImage->image()->first());
    }
}
