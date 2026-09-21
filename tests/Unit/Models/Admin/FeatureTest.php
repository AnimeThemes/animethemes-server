<?php

declare(strict_types=1);

use App\Models\Admin\Feature;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('nameable', function (): void {
    $feature = Feature::factory()->createOne();

    expect($feature->getName())->toBeString();
});

test('has subtitle', function (): void {
    $feature = Feature::factory()->createOne();

    expect($feature->getSubtitle())->toBeString();
});

test('nullable scope', function (): void {
    $feature = Feature::factory()->createOne();

    expect($feature->isNullScope())->toBeTrue();
});

test('non null scope', function (): void {
    $feature = Feature::factory()->createOne([
        Feature::ATTRIBUTE_SCOPE => fake()->word(),
    ]);

    expect($feature->isNullScope())->toBeFalse();
});
