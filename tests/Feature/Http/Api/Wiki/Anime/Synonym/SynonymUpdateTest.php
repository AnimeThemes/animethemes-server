<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $parameters = AnimeSynonym::factory()->raw();

    $response = put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $parameters = AnimeSynonym::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $synonym = AnimeSynonym::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $parameters = AnimeSynonym::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $parameters = AnimeSynonym::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animesynonym.update', ['animesynonym' => $synonym] + $parameters));

    $response->assertOk();
});
