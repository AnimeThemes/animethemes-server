<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\UseBuilderDirective;
use App\GraphQL\Builders\Admin\DumpBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\DumpType;

#[UseBuilderDirective(DumpBuilder::class)]
class DumpsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('dumps');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of dump resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): DumpType
    {
        return new DumpType();
    }
}
