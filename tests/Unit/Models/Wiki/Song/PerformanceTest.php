<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
        ->artist(Artist::factory()->createOne())
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $performance->artist());
    $this->assertInstanceOf(Artist::class, $performance->artist()->first());
});

test('membership', function () {
    $performance = Performance::factory()
        ->artist(Membership::factory()->createOne())
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $performance->membership());
    $this->assertInstanceOf(Membership::class, $performance->membership()->first());
});
