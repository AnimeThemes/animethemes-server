<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('video', function (): void {
    $entryVideo = EntryVideo::factory()
        ->for(Video::factory())
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne();

    expect($entryVideo->video())->toBeInstanceOf(BelongsTo::class);
    expect($entryVideo->video()->first())->toBeInstanceOf(Video::class);
});

test('entry', function (): void {
    $entryVideo = EntryVideo::factory()
        ->for(Video::factory())
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne();

    expect($entryVideo->entry())->toBeInstanceOf(BelongsTo::class);
    expect($entryVideo->entry()->first())->toBeInstanceOf(Entry::class);
});
