<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\AnimeWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class AnimeStoreRequest.
 */
class AnimeStoreRequest extends EloquentStoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AnimeWriteQuery($this->validated());
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'anime:create';
    }
}
