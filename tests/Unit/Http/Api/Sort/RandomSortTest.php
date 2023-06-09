<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Sort\RandomSort;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class RandomSortTest.
 */
class RandomSortTest extends TestCase
{
    use WithFaker;

    /**
     * The Random Sort shall be formatted as "{key}" for the any Direction.
     *
     * @return void
     */
    public function testFormat(): void
    {
        $sort = new RandomSort();

        $direction = Arr::random(Direction::cases());

        static::assertEquals(RandomCriteria::PARAM_VALUE, $sort->format($direction));
    }
}
