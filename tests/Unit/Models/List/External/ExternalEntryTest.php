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

    $this->assertInstanceOf(ExternalEntryStatus::class, $status);
});

test('casts is favorite to bool', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $is_favorite = $entry->is_favorite;

    $this->assertIsBool($is_favorite);
});

test('nameable', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $this->assertIsString($entry->getName());
});

test('has subtitle', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($entry->getSubtitle());
});

test('profile', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $entry->externalprofile());
    $this->assertInstanceOf(ExternalProfile::class, $entry->externalprofile()->first());
});

test('anime', function (): void {
    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory())
        ->for(Anime::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $entry->anime());
    $this->assertInstanceOf(Anime::class, $entry->anime()->first());
});
