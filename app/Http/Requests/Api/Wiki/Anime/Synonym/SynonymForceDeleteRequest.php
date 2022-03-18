<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Synonym;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\Anime\SynonymQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Requests\Api\Base\EloquentForceDeleteRequest;

/**
 * Class SynonymForceDeleteRequest.
 */
class SynonymForceDeleteRequest extends EloquentForceDeleteRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new SynonymSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new SynonymQuery();
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'synonym:delete';
    }
}
