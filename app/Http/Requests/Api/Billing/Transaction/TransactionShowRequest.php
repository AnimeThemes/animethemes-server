<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Query;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Resource\TransactionResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class TransactionShowRequest.
 */
class TransactionShowRequest extends ShowRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    protected function getResource(): BaseResource
    {
        return TransactionResource::make(new MissingValue(), Query::make());
    }
}
