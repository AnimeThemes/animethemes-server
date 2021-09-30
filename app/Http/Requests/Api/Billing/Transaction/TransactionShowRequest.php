<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class TransactionShowRequest.
 */
class TransactionShowRequest extends ShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new TransactionSchema();
    }
}
