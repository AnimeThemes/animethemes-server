<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->makeOne();

    $response = $this->post(route('api.animesynonym.store', $synonym->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animesynonym.store', $synonym->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animesynonym.store'));

    $response->assertJsonValidationErrors([
        AnimeSynonym::ATTRIBUTE_TEXT,
    ]);
});

test('create', function () {
    $anime = Anime::factory()->createOne();

    $parameters = array_merge(
        AnimeSynonym::factory()->raw(),
        [AnimeSynonym::ATTRIBUTE_ANIME => $anime->getKey()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.animesynonym.store', $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(AnimeSynonym::class, 1);
});
