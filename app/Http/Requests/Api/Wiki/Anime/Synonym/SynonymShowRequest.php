<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Synonym;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SynonymShowRequest.
 */
class SynonymShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return SynonymResource::make(new MissingValue(), Query::make());
    }
}
