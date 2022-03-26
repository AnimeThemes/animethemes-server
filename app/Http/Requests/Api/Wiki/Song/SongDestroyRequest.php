<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Song;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Song\SongWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class SongDestroyRequest.
 */
class SongDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new SongSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new SongWriteQuery($this->validated());
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'song:delete';
    }
}
