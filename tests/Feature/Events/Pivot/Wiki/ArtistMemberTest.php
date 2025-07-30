<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\Wiki\ArtistMember\ArtistMemberUpdated;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('artist member created event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $artist->members()->attach($member);

    Event::assertDispatched(ArtistMemberCreated::class);
});

test('artist member deleted event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $artist->members()->attach($member);
    $artist->members()->detach($member);

    Event::assertDispatched(ArtistMemberDeleted::class);
});

test('artist member updated event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $artistMember = ArtistMember::factory()
        ->for($artist, 'artist')
        ->for($member, 'member')
        ->createOne();

    $changes = ArtistMember::factory()
        ->for($artist, 'artist')
        ->for($member, 'member')
        ->makeOne();

    $artistMember->fill($changes->getAttributes());
    $artistMember->save();

    Event::assertDispatched(ArtistMemberUpdated::class);
});

test('artist member updated event embed fields', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $artistMember = ArtistMember::factory()
        ->for($artist, 'artist')
        ->for($member, 'member')
        ->createOne();

    $changes = ArtistMember::factory()
        ->for($artist, 'artist')
        ->for($member, 'member')
        ->makeOne();

    $artistMember->fill($changes->getAttributes());
    $artistMember->save();

    Event::assertDispatched(ArtistMemberUpdated::class, function (ArtistMemberUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
