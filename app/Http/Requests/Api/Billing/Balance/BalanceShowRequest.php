<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Balance;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Resource\BalanceResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class BalanceShowRequest.
 */
class BalanceShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return BalanceResource::make(new MissingValue(), Query::make());
    }
}
