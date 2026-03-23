<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Admin;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Admin\AnnouncementType;
use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementPaginationQuery extends EloquentPaginationQuery
{
    public function name(): string
    {
        return 'announcementPagination';
    }

    public function description(): string
    {
        return 'Returns a listing of announcement resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AnnouncementType
    {
        return new AnnouncementType();
    }

    /**
     * @param  Builder<Announcement>  $builder
     * @return Builder<Announcement>
     */
    protected function query(Builder $builder, array $args): Builder
    {
        return $builder->current();
    }
}
