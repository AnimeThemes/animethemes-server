<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\ImageType;

class ImagePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('imagePagination');
    }

    public function description(): string
    {
        return 'Returns a listing of images resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ImageType
    {
        return new ImageType();
    }
}
