<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $response = delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

    $response->assertForbidden();
});

test('deleted', function () {
    $synonym = AnimeSynonym::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(AnimeSynonym::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animesynonym.forceDelete', ['animesynonym' => $synonym]));

    $response->assertOk();
    $this->assertModelMissing($synonym);
});
