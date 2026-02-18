<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Synonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $response = delete(route('api.synonym.forceDelete', ['synonym' => $synonym]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.synonym.forceDelete', ['synonym' => $synonym]));

    $response->assertForbidden();
});

test('deleted', function () {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Synonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.synonym.forceDelete', ['synonym' => $synonym]));

    $response->assertOk();
    $this->assertModelMissing($synonym);
});
