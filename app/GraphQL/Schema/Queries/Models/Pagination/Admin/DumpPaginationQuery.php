<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Admin;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Admin\DumpType;
use Illuminate\Database\Eloquent\Builder;

class DumpPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('dumpPagination');
    }

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

    protected function query(Builder $builder, array $args): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->onlySafeDumps();
    }
}
