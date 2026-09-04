<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = Entry::factory()->raw();

    $response = put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = Entry::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $entry = Entry::factory()
        ->trashed()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = Entry::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Entry::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertNotFound();
});

test('update', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $parameters = Entry::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Entry::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animethemeentry.update', ['animethemeentry' => $entry] + $parameters));

    $response->assertOk();
});
