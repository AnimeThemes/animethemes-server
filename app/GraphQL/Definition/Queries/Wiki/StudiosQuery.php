<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\StudioType;

#[UseSearchDirective]
class StudiosQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('studios');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of studio resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): StudioType
    {
        return new StudioType();
    }
}
