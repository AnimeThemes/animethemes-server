<?php

declare(strict_types=1);

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('casts watch status to enum', function () {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $status = $entry->watch_status;

    static::assertInstanceOf(ExternalEntryWatchStatus::class, $status);
});

test('casts is favorite to bool', function () {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $is_favorite = $entry->is_favorite;

    static::assertIsBool($is_favorite);
});

test('nameable', function () {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    static::assertIsString($entry->getName());
});

test('has subtitle', function () {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->for(Anime::factory())
        ->createOne();

    static::assertIsString($entry->getSubtitle());
});

test('profile', function () {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $entry->externalprofile());
    static::assertInstanceOf(ExternalProfile::class, $entry->externalprofile()->first());
});

test('anime', function () {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->for(Anime::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $entry->anime());
    static::assertInstanceOf(Anime::class, $entry->anime()->first());
});
