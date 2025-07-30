<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $parameters = ArtistMember::factory()->raw();

    $response = $this->put(route('api.artistmember.update', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $parameters = ArtistMember::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.artistmember.update', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $parameters = ArtistMember::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.artistmember.update', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

    $response->assertOk();
});
