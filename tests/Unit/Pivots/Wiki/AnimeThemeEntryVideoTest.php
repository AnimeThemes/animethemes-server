<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('video', function () {
    $animeThemeEntryVideo = AnimeThemeEntryVideo::factory()
        ->for(Video::factory())
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->video());
    static::assertInstanceOf(Video::class, $animeThemeEntryVideo->video()->first());
});

test('entry', function () {
    $animeThemeEntryVideo = AnimeThemeEntryVideo::factory()
        ->for(Video::factory())
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->animethemeentry());
    static::assertInstanceOf(AnimeThemeEntry::class, $animeThemeEntryVideo->animethemeentry()->first());
});
