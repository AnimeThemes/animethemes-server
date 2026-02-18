<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Synonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $synonym = Synonym::factory()
        ->trashed()
        ->forAnime()
        ->createOne();

    $response = patch(route('api.synonym.restore', ['synonym' => $synonym]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = Synonym::factory()
        ->trashed()
        ->forAnime()
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.synonym.restore', ['synonym' => $synonym]));

    $response->assertForbidden();
});

test('trashed', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.synonym.restore', ['synonym' => $synonym]));

    $response->assertForbidden();
});

test('restored', function () {
    $synonym = Synonym::factory()
        ->trashed()
        ->forAnime()
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.synonym.restore', ['synonym' => $synonym]));

    $response->assertOk();
    $this->assertNotSoftDeleted($synonym);
});
