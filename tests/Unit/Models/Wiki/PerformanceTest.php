<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Performance;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('nameable', function (): void {
    $performance = Performance::factory()->createOne();

    expect($performance->getName())->toBeString();
});

test('has subtitle', function (): void {
    $performance = Performance::factory()->createOne();

    expect($performance->getSubtitle())->toBeString();
});

test('song', function (): void {
    $performance = Performance::factory()
        ->for(Song::factory())
        ->createOne();

    expect($performance->song())->toBeInstanceOf(BelongsTo::class);
    expect($performance->song()->first())->toBeInstanceOf(Song::class);
});

test('artist', function (): void {
    $performance = Performance::factory()
        ->for(Artist::factory()->createOne(), Performance::RELATION_ARTIST)
        ->createOne();

    expect($performance->artist())->toBeInstanceOf(BelongsTo::class);
    expect($performance->artist()->first())->toBeInstanceOf(Artist::class);
});

test('member', function (): void {
    $performance = Performance::factory()
        ->for(Artist::factory(), Performance::RELATION_ARTIST)
        ->for(Artist::factory(), Performance::RELATION_MEMBER)
        ->createOne();

    expect($performance->member())->toBeInstanceOf(BelongsTo::class);
    expect($performance->member()->first())->toBeInstanceOf(Artist::class);
});
