<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $response = delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $theme = Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertNotFound();
});

test('deleted', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Theme::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertOk();
    $this->assertSoftDeleted($theme);
});
