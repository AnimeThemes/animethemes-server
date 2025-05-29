<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Anime;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeSynonymType;
use GraphQL\Type\Definition\Type;

/**
 * Class AnimeSynonymsQuery.
 */
class AnimeSynonymsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('animesynonyms');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of anime synonyms resources given fields.';
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

            'orderBy: _ @orderBy(columnsEnum: "AnimeSynonymColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return Type
     */
    public function baseType(): Type
    {
        return new AnimeSynonymType();
    }
}
