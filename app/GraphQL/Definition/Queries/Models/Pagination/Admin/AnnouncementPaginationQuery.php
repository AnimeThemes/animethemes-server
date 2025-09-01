<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Admin;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Admin\AnnouncementType;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('announcementPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of announcement resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AnnouncementType
    {
        return new AnnouncementType();
    }

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        /** @phpstan-ignore-next-line */
        return $builder->public();
    }
}
