<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Balance;

use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class BalanceShowRequest.
 */
class BalanceShowRequest extends ShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new BalanceSchema();
    }
}
