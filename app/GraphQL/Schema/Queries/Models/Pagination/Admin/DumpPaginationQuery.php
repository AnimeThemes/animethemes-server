<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Admin;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Admin\DumpType;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Builder;

class DumpPaginationQuery extends EloquentPaginationQuery
{
    public function name(): string
    {
        return 'dumpPagination';
    }

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

    /**
     * @param  Builder<Dump>  $builder
     * @return Builder<Dump>
     */
    protected function query(Builder $builder, array $args): Builder
    {
        return $builder->public();
    }
}
