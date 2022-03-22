<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\Anime\ThemeQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class ThemeDestroyRequest.
 */
class ThemeDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ThemeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new ThemeQuery();
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'theme:delete';
    }
}
