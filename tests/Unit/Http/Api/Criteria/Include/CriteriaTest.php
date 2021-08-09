<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Include;

use App\Http\Api\Criteria\Include\Criteria;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class CriteriaTest.
 */
class CriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * The Include Criteria shall return the intersection of allowed and specified include paths.
     *
     * @return void
     */
    public function testIsAllowedIncludePath()
    {
        $paths = collect($this->faker->words($this->faker->randomDigitNotNull()));

        $allowedIncludePaths = [$paths->random()];

        $criteria = new Criteria($paths);

        static::assertTrue($criteria->getAllowedPaths($allowedIncludePaths)->count() === 1);
    }
}
