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

    $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

    $response->assertForbidden();
});

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artist, 'member' => $member]));

    $response->assertNotFound();
});

test('deleted', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistmember.destroy', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

    $response->assertOk();
    static::assertModelMissing($artistMember);
});
