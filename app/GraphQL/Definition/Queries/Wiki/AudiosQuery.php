<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\AudioType;

class AudiosQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('audios');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of audio resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): AudioType
    {
        return new AudioType();
    }
}
