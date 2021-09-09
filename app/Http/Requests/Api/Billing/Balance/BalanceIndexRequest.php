<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Balance;

use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Collection\BalanceCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class BalanceIndexRequest.
 */
class BalanceIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return BalanceCollection::make(new MissingValue(), Query::make());
    }
}
