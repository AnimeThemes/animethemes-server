<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->makeOne();

    $response = post(route('api.animethemeentry.store', $entry->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animethemeentry.store', $entry->toArray()));

    $response->assertForbidden();
});

test('required fields', function (): void {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Entry::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animethemeentry.store'));

    $response->assertJsonValidationErrors([
        Entry::ATTRIBUTE_THEME,
    ]);
});

test('create', function (): void {
    $theme = Theme::factory()->for(Anime::factory())->createOne();

    $parameters = array_merge(
        Entry::factory()->raw(),
        [Entry::ATTRIBUTE_THEME => $theme->getKey()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Entry::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animethemeentry.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Entry::class, 1);
});
