<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('nameable', function (): void {
    $staff = SongStaff::factory()->createOne();

    expect($staff->getName())->toBeString();
});

test('has subtitle', function (): void {
    $staff = SongStaff::factory()->createOne();

    expect($staff->getSubtitle())->toBeString();
});

test('song', function (): void {
    $staff = SongStaff::factory()
        ->for(Song::factory())
        ->createOne();

    expect($staff->song())->toBeInstanceOf(BelongsTo::class);
    expect($staff->song()->first())->toBeInstanceOf(Song::class);
});

test('artist', function (): void {
    $staff = SongStaff::factory()
        ->for(Artist::factory()->createOne(), SongStaff::RELATION_ARTIST)
        ->createOne();

    expect($staff->artist())->toBeInstanceOf(BelongsTo::class);
    expect($staff->artist()->first())->toBeInstanceOf(Artist::class);
});

test('member', function (): void {
    $staff = SongStaff::factory()
        ->for(Artist::factory(), SongStaff::RELATION_ARTIST)
        ->for(Artist::factory(), SongStaff::RELATION_MEMBER)
        ->createOne();

    expect($staff->member())->toBeInstanceOf(BelongsTo::class);
    expect($staff->member()->first())->toBeInstanceOf(Artist::class);
});
