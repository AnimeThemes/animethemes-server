<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $response = delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

    $response->assertForbidden();
});

test('trashed', function () {
    $synonym = AnimeSynonym::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

    $response->assertNotFound();
});

test('deleted', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animesynonym.destroy', ['animesynonym' => $synonym]));

    $response->assertOk();
    $this->assertSoftDeleted($synonym);
});
