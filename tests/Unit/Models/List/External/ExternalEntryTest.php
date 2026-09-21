<?php

declare(strict_types=1);

use App\Enums\Models\List\ExternalEntryStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('casts status to enum', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $status = $entry->status;

    expect($status)->toBeInstanceOf(ExternalEntryStatus::class);
});

test('casts is favorite to bool', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $is_favorite = $entry->is_favorite;

    expect($is_favorite)->toBeBool();
});

test('nameable', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    expect($entry->getName())->toBeString();
});

test('has subtitle', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->for(Anime::factory())
        ->createOne();

    expect($entry->getSubtitle())->toBeString();
});

test('profile', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    expect($entry->externalprofile())->toBeInstanceOf(BelongsTo::class);
    expect($entry->externalprofile()->first())->toBeInstanceOf(ExternalProfile::class);
});

test('anime', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->for(Anime::factory())
        ->createOne();

    expect($entry->anime())->toBeInstanceOf(BelongsTo::class);
    expect($entry->anime()->first())->toBeInstanceOf(Anime::class);
});
