<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class TrashedCriteriaTest.
 */
class TrashedCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * The Trashed Criteria shall parse the field.
     *
     * @return void
     */
    public function testField(): void
    {
        $criteria = TrashedCriteria::make(new GlobalScope(), TrashedCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(TrashedCriteria::PARAM_VALUE, $criteria->getField());
    }
}
