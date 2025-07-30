<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $theme = AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $theme = AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertForbidden();
});

test('trashed', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertForbidden();
});

test('restored', function () {
    $theme = AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertOk();
    $this->assertNotSoftDeleted($theme);
});
