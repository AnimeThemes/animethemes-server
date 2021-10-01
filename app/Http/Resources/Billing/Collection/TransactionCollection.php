<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Collection;

use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\Schema;
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
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Transaction::class;

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
        return $this->collection->map(function (Transaction $transaction) {
            return TransactionResource::make($transaction, $this->query);
        })->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new TransactionSchema();
    }
}
