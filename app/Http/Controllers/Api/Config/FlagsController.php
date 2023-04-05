<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Config\FlagsSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Config\Resource\FlagsResource;

/**
 * Class FlagsController.
 */
class FlagsController extends Controller implements InteractsWithSchema
{
    /**
     * Flags resource.
     *
     * @param  ShowRequest  $request
     * @return FlagsResource
     */
    public function show(ShowRequest $request): FlagsResource
    {
        $query = new Query($request->validated());

        return new FlagsResource($query);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new FlagsSchema();
    }
}
