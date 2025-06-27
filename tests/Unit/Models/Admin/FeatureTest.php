<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Feature;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    use WithFaker;

    /**
     * Features shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $feature = Feature::factory()->createOne();

        static::assertIsString($feature->getName());
    }

    /**
     * Features shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $feature = Feature::factory()->createOne();

        static::assertIsString($feature->getSubtitle());
    }

    /**
     * Feature shall indicate if the scope is null.
     *
     * @return void
     */
    public function test_nullable_scope(): void
    {
        $feature = Feature::factory()->createOne();

        static::assertTrue($feature->isNullScope());
    }

    /**
     * Feature shall indicate if the scope is not null.
     *
     * @return void
     */
    public function test_non_null_scope(): void
    {
        $feature = Feature::factory()->createOne([
            Feature::ATTRIBUTE_SCOPE => $this->faker->word(),
        ]);

        static::assertFalse($feature->isNullScope());
    }
}
