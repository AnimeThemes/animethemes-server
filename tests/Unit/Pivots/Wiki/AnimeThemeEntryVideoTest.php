<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class AnimeThemeEntryVideoTest extends TestCase
{
    /**
     * An AnimeThemeEntryVideo shall belong to a Video.
     */
    public function testVideo(): void
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
     */
    public function testEntry(): void
    {
        $animeThemeEntryVideo = AnimeThemeEntryVideo::factory()
            ->for(Video::factory())
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->animethemeentry());
        static::assertInstanceOf(AnimeThemeEntry::class, $animeThemeEntryVideo->animethemeentry()->first());
    }
}
