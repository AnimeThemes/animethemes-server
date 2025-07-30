<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function Pest\Laravel\get;

use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('should not apply if no allowed paths', function () {
    $criteria = FakeCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, Str::random());

    $schema = new class() extends Schema
    {
        /**
         * Get the type of the resource.
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
        fake()->randomDigitNotNull(),
        fn () => new AllowedInclude($schema, fake()->word())
    );

    $filter = new HasFilter($allowedIncludes->all());

    $this->assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should apply if allowed paths', function () {
    $schema = new class() extends Schema
    {
        /**
         * Get the type of the resource.
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
        fake()->randomDigitNotNull(),
        fn () => new AllowedInclude($schema, fake()->word())
    );

    /** @var AllowedInclude $selectedInclude */
    $selectedInclude = $allowedIncludes->random();

    $criteria = FakeCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, $selectedInclude->path());

    $filter = new HasFilter($allowedIncludes->all());

    $this->assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
});
