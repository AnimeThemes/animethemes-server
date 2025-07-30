<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $parameters = ArtistMember::factory()->raw();

    $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $parameters = ArtistMember::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

    $response->assertForbidden();
});

test('create', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $parameters = ArtistMember::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artistmember.store', ['artist' => $artist, 'member' => $member] + $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(ArtistMember::class, 1);
});
