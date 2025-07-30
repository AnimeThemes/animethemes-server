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
        ->makeOne();

    $response = $this->post(route('api.animethemeentry.store', $entry->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animethemeentry.store', $entry->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animethemeentry.store'));

    $response->assertJsonValidationErrors([
        AnimeThemeEntry::ATTRIBUTE_THEME,
    ]);
});

test('create', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $parameters = array_merge(
        AnimeThemeEntry::factory()->raw(),
        [AnimeThemeEntry::ATTRIBUTE_THEME => $theme->getKey()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(AnimeThemeEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animethemeentry.store', $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(AnimeThemeEntry::class, 1);
});
