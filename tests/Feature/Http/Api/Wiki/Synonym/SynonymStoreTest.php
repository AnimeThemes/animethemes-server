<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $synonym = Synonym::factory()->forAnime()->makeOne();

    $response = post(route('api.synonym.store', $synonym->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = Synonym::factory()->forAnime()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.synonym.store', $synonym->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.synonym.store'));

    $response->assertJsonValidationErrors([
        Synonym::ATTRIBUTE_TEXT,
    ]);
});

test('create', function () {
    $anime = Anime::factory()->createOne();

    $parameters = array_merge(
        Synonym::factory()->raw(),
        [
            Synonym::ATTRIBUTE_SYNONYMABLE_ID => $anime->getKey(),
            Synonym::ATTRIBUTE_SYNONYMABLE_TYPE => 'anime',
        ],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.synonym.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Synonym::class, 1);
});
