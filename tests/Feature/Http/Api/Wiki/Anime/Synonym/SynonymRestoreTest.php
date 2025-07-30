<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $synonym = AnimeSynonym::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $response = patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = AnimeSynonym::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

    $response->assertForbidden();
});

test('trashed', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

    $response->assertForbidden();
});

test('restored', function () {
    $synonym = AnimeSynonym::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.animesynonym.restore', ['animesynonym' => $synonym]));

    $response->assertOk();
    $this->assertNotSoftDeleted($synonym);
});
