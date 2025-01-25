<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

/**
 * Class HasFilterTest.
 */
class HasFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not allowed paths are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoAllowedPaths(): void
    {
        $criteria = FakeCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, Str::random());

        $schema = new class() extends Schema
        {
            /**
             * Get the type of the resource.
             *
             * @return string
             */
            public function type(): string
            {
                return Str::random();
            }

            /**
             * Get the allowed includes.
             *
             * @return AllowedInclude[]
             */
            public function allowedIncludes(): array
            {
                return [];
            }

            /**
             * Get the direct fields of the resource.
             *
             * @return Field[]
             *
             * @noinspection PhpMissingParentCallCommonInspection
             */
            public function fields(): array
            {
                return [];
            }
        };

        $allowedIncludes = Collection::times(
            $this->faker->randomDigitNotNull(),
            fn () => new AllowedInclude($schema, $this->faker->word())
        );

        $filter = new HasFilter($allowedIncludes->all());

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If values that are allowed paths are specified for the key, apply the filter.
     *
     * @return void
     */
    public function testShouldApplyIfAllowedPaths(): void
    {
        $schema = new class() extends Schema
        {
            /**
             * Get the type of the resource.
             *
             * @return string
             */
            public function type(): string
            {
                return Str::random();
            }

            /**
             * Get the allowed includes.
             *
             * @return AllowedInclude[]
             */
            public function allowedIncludes(): array
            {
                return [];
            }

            /**
             * Get the direct fields of the resource.
             *
             * @return Field[]
             *
             * @noinspection PhpMissingParentCallCommonInspection
             */
            public function fields(): array
            {
                return [];
            }
        };

        $allowedIncludes = Collection::times(
            $this->faker->randomDigitNotNull(),
            fn () => new AllowedInclude($schema, $this->faker->word())
        );

        /** @var AllowedInclude $selectedInclude */
        $selectedInclude = $allowedIncludes->random();

        $criteria = FakeCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, $selectedInclude->path());

        $filter = new HasFilter($allowedIncludes->all());

        static::assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
    }
}
