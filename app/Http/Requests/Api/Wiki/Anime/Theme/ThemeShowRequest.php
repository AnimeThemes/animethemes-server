<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ThemeShowRequest.
 */
class ThemeShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return ThemeResource::make(new MissingValue(), Query::make());
    }
}
