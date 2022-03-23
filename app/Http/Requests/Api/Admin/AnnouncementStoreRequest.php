<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Api\Query\Admin\AnnouncementQuery;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class AnnouncementStoreRequest.
 */
class AnnouncementStoreRequest extends EloquentStoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnnouncementSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new AnnouncementQuery();
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'announcement:create';
    }
}
