<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Synonym;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Anime\Synonym\SynonymWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class SynonymStoreRequest.
 */
class SynonymStoreRequest extends EloquentStoreRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new SynonymWriteQuery($this->validated());
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'synonym:create';
    }
}
