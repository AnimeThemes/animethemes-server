<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Document;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Document\PageType;

class PagesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('pages');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of page resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PageType
    {
        return new PageType();
    }
}
