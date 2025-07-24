<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Feature;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeatureTest extends TestCase
{
    use WithFaker;

    /**
     * Features shall be nameable.
     */
    public function testNameable(): void
    {
        $feature = Feature::factory()->createOne();

        static::assertIsString($feature->getName());
    }

    /**
     * Features shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $feature = Feature::factory()->createOne();

        static::assertIsString($feature->getSubtitle());
    }

    /**
     * Feature shall indicate if the scope is null.
     */
    public function testNullableScope(): void
    {
        $feature = Feature::factory()->createOne();

        static::assertTrue($feature->isNullScope());
    }

    /**
     * Feature shall indicate if the scope is not null.
     */
    public function testNonNullScope(): void
    {
        $feature = Feature::factory()->createOne([
            Feature::ATTRIBUTE_SCOPE => $this->faker->word(),
        ]);

        static::assertFalse($feature->isNullScope());
    }
}
