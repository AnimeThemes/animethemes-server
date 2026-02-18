<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Synonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $parameters = Synonym::factory()->raw();

    $response = put(route('api.synonym.update', ['synonym' => $synonym] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $parameters = Synonym::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.synonym.update', ['synonym' => $synonym] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $synonym = Synonym::factory()
        ->trashed()
        ->forAnime()
        ->createOne();

    $parameters = Synonym::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.synonym.update', ['synonym' => $synonym] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $parameters = Synonym::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.synonym.update', ['synonym' => $synonym] + $parameters));

    $response->assertOk();
});
