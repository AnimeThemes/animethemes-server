<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function () {
    $performance = Performance::factory()->createOne();

    $this->assertIsString($performance->getName());
});

test('has subtitle', function () {
    $performance = Performance::factory()->createOne();

    $this->assertIsString($performance->getSubtitle());
});

test('song', function () {
    $performance = Performance::factory()
        ->for(Song::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $performance->song());
    $this->assertInstanceOf(Song::class, $performance->song()->first());
});

test('artist', function () {
    $performance = Performance::factory()
        ->for(Artist::factory()->createOne(), Performance::RELATION_ARTIST)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $performance->artist());
    $this->assertInstanceOf(Artist::class, $performance->artist()->first());
});

test('member', function () {
    $performance = Performance::factory()
        ->for(Artist::factory(), Performance::RELATION_ARTIST)
        ->for(Artist::factory(), Performance::RELATION_MEMBER)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $performance->member());
    $this->assertInstanceOf(Artist::class, $performance->member()->first());
});
