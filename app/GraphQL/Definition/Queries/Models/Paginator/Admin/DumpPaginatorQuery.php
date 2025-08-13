<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\DumpType;
use Illuminate\Database\Eloquent\Builder;

class DumpPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('dumpPaginator');
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
    public function baseRebingType(): DumpType
    {
        return new DumpType();
    }

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->onlySafeDumps();
    }
}
