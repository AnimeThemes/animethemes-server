<?php

declare(strict_types=1);

use App\Models\Admin\Feature;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function (): void {
    $feature = Feature::factory()->createOne();

    $this->assertIsString($feature->getName());
});

test('has subtitle', function (): void {
    $feature = Feature::factory()->createOne();

    $this->assertIsString($feature->getSubtitle());
});

test('nullable scope', function (): void {
    $feature = Feature::factory()->createOne();

    $this->assertTrue($feature->isNullScope());
});

test('non null scope', function (): void {
    $feature = Feature::factory()->createOne([
        Feature::ATTRIBUTE_SCOPE => fake()->word(),
    ]);

    $this->assertFalse($feature->isNullScope());
});
