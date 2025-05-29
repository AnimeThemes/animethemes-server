<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use GraphQL\Type\Definition\Type;

/**
 * Class ImagesQuery.
 */
class ImagesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('images');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of images resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "ImageColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new ImageType();
    }
}
