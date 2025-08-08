<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Controllers\Admin\AnnouncementController;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\AnnouncementType;

#[UseBuilderDirective(AnnouncementController::class)]
#[UsePaginateDirective]
class AnnouncementPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('announcementPaginator');
    }

    /**
     * The description of the type.
     */
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
}
