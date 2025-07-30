<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $entry = AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $entry = AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertForbidden();
});

test('trashed', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertForbidden();
});

test('restored', function () {
    $entry = AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertOk();
    $this->assertNotSoftDeleted($entry);
});
