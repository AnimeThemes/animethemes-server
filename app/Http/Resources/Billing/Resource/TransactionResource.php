<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class TransactionResource.
 */
class TransactionResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'transaction';

    /**
     * Create a new resource instance.
     *
     * @param  Transaction | MissingValue | null  $transaction
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Transaction|MissingValue|null $transaction, ReadQuery $query)
    {
        parent::__construct($transaction, $query);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new TransactionSchema();
    }
}
