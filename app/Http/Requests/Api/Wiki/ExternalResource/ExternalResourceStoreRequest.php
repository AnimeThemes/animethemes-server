<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\ExternalResource;

use App\Enums\BaseEnum;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\ExternalResourceQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceStoreRequest.
 */
class ExternalResourceStoreRequest extends EloquentStoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ExternalResourceSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new ExternalResourceQuery();
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'resource:create';
    }

    /**
     * The list of enum attributes to convert.
     *
     * @return array<string, class-string<BaseEnum>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function enums(): array
    {
        return [
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::class,
        ];
    }
}