<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme\Entry;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\Anime\Theme\EntryQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Requests\Api\Base\EloquentForceDeleteRequest;

/**
 * Class EntryForceDeleteRequest.
 */
class EntryForceDeleteRequest extends EloquentForceDeleteRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new EntrySchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new EntryQuery();
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'entry:delete';
    }
}
