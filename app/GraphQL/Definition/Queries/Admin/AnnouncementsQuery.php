<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\UseBuilderDirective;
use App\GraphQL\Builders\Admin\AnnouncementBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\AnnouncementType;

#[UseBuilderDirective(AnnouncementBuilder::class)]
class AnnouncementsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('announcements');
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
