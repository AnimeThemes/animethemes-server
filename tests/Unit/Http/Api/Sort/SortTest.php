<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SortTest.
 */
class SortTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the sort column shall be derived from the sort key.
     *
     * @return void
     */
    public function testDefaultColumn()
    {
        $sort = new Sort($this->faker->word());

        static::assertEquals($sort->getKey(), $sort->getColumn());
    }

    /**
     * The Sort shall be formatted as "{key}" for the Ascending Direction.
     *
     * @return void
     */
    public function testFormatAsc()
    {
        $sortField = $this->faker->word();

        $sort = new Sort($sortField);

        static::assertEquals($sortField, $sort->format(Direction::ASCENDING()));
    }

    /**
     * The Sort shall be formatted as "-{key}" for the Descending Direction.
     *
     * @return void
     */
    public function testFormatDesc()
    {
        $sortField = $this->faker->word();

        $sort = new Sort($sortField);

        $descendingSortField = Str::of('-')->append($sortField)->__toString();

        static::assertEquals($descendingSortField, $sort->format(Direction::DESCENDING()));
    }
}
