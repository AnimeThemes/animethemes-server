<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $response = delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

    $response->assertForbidden();
});

test('trashed', function () {
    $entry = AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

    $response->assertNotFound();
});

test('deleted', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animethemeentry.destroy', ['animethemeentry' => $entry]));

    $response->assertOk();
    $this->assertSoftDeleted($entry);
});
