<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function (): void {
    $entry = Entry::factory()
        ->trashed()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $entry = Entry::factory()
        ->trashed()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Entry::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertOk();
});

test('restored', function (): void {
    $entry = Entry::factory()
        ->trashed()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Entry::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animethemeentry.restore', ['animethemeentry' => $entry]));

    $response->assertOk();
    $this->assertNotSoftDeleted($entry);
});
