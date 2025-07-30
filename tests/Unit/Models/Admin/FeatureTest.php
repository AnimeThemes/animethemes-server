<?php

declare(strict_types=1);

use App\Models\Admin\Feature;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $feature = Feature::factory()->createOne();

    static::assertIsString($feature->getName());
});

test('has subtitle', function () {
    $feature = Feature::factory()->createOne();

    static::assertIsString($feature->getSubtitle());
});

test('nullable scope', function () {
    $feature = Feature::factory()->createOne();

    static::assertTrue($feature->isNullScope());
});

test('non null scope', function () {
    $feature = Feature::factory()->createOne([
        Feature::ATTRIBUTE_SCOPE => fake()->word(),
    ]);

    static::assertFalse($feature->isNullScope());
});
