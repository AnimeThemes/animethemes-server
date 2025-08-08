<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\Document;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Controllers\Document\PageController;
use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\Document\PageType;

#[UseBuilderDirective(PageController::class, 'show')]
class PageQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('page');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a page resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PageType
    {
        return new PageType();
    }
}
