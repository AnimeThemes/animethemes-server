<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = AnimeThemeEntry::factory()->raw();

    $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = AnimeThemeEntry::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $entry = AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = AnimeThemeEntry::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = AnimeThemeEntry::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertOk();
});
