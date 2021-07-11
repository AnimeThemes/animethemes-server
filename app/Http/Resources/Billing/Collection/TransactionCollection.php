<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Collection;

use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionAmountFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionDateFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionDescriptionFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionExternalIdFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionServiceFilter;
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
     * @var string
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
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Transaction $transaction) {
            return TransactionResource::make($transaction, $this->parser);
        })->all();
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [
            'transaction_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'date',
            'service',
            'description',
            'amount',
            'external_id',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return [
            TransactionDateFilter::class,
            TransactionServiceFilter::class,
            TransactionDescriptionFilter::class,
            TransactionAmountFilter::class,
            TransactionExternalIdFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
