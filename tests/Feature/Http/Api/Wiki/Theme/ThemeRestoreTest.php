<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function (): void {
    $theme = Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $theme = Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertOk();
});

test('restored', function (): void {
    $theme = Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animetheme.restore', ['animetheme' => $theme]));

    $response->assertOk();
    $this->assertNotSoftDeleted($theme);
});
