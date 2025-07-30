<?php

declare(strict_types=1);

use App\Models\Admin\Feature;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $feature = Feature::factory()->createOne();

    $this->assertIsString($feature->getName());
});

test('has subtitle', function () {
    $feature = Feature::factory()->createOne();

    $this->assertIsString($feature->getSubtitle());
});

test('nullable scope', function () {
    $feature = Feature::factory()->createOne();

    $this->assertTrue($feature->isNullScope());
});

test('non null scope', function () {
    $feature = Feature::factory()->createOne([
        Feature::ATTRIBUTE_SCOPE => fake()->word(),
    ]);

    $this->assertFalse($feature->isNullScope());
});
