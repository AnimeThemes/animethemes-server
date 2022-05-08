<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\Request;

/**
 * Class TransactionCollection.
 */
class TransactionCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'transactions';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(
            fn (Transaction $transaction) => new TransactionResource($transaction, $this->query)
        )->all();
    }
}
