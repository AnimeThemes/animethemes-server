<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function () {
    $membership = Membership::factory()->createOne();

    $this->assertIsString($membership->getName());
});

test('has subtitle', function () {
    $membership = Membership::factory()->createOne();

    $this->assertIsString($membership->getSubtitle());
});

test('group', function () {
    $membership = Membership::factory()
        ->for(Artist::factory(), Membership::RELATION_GROUP)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $membership->group());
    $this->assertInstanceOf(Artist::class, $membership->group()->first());
});

test('member', function () {
    $membership = Membership::factory()
        ->for(Artist::factory(), Membership::RELATION_MEMBER)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $membership->member());
    $this->assertInstanceOf(Artist::class, $membership->member()->first());
});

test('performances', function () {
    $performanceCount = fake()->randomDigitNotNull();

    $membership = Membership::factory()
        ->has(Performance::factory()->count($performanceCount))
        ->createOne();

    $this->assertInstanceOf(MorphMany::class, $membership->performances());
    $this->assertEquals($performanceCount, $membership->performances()->count());
    $this->assertInstanceOf(Performance::class, $membership->performances()->first());
});
