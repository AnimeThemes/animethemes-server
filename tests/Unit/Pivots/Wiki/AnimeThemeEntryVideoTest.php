<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('video', function (): void {
    $animeThemeEntryVideo = EntryVideo::factory()
        ->for(Video::factory())
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->video());
    $this->assertInstanceOf(Video::class, $animeThemeEntryVideo->video()->first());
});

test('entry', function (): void {
    $animeThemeEntryVideo = EntryVideo::factory()
        ->for(Video::factory())
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeThemeEntryVideo->animethemeentry());
    $this->assertInstanceOf(Entry::class, $animeThemeEntryVideo->animethemeentry()->first());
});
