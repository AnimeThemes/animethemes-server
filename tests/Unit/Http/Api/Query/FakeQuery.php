<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Query;

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Support\Str;

/**
 * Class FakeQuery.
 */
class FakeQuery extends Query
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new class() extends Schema
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
    }
}
