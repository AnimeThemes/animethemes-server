<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoTest.
 */
class AnimeThemeEntryVideoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An AnimeThemeEntryVideo shall belong to a Video.
     *
     * @return void
     */
    public function testVideo()
    {
        $animeThemeEntryVideo = AnimeThemeEntryVideo::factory()
            ->for(Video::factory())
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->video());
        static::assertInstanceOf(Video::class, $animeThemeEntryVideo->video()->first());
    }

    /**
     * An AnimeThemeEntryVideo shall belong to an Entry.
     *
     * @return void
     */
    public function testEntry()
    {
        $animeThemeEntryVideo = AnimeThemeEntryVideo::factory()
            ->for(Video::factory())
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->animethemeentry());
        static::assertInstanceOf(AnimeThemeEntry::class, $animeThemeEntryVideo->animethemeentry()->first());
    }
}
