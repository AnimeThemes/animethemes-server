<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class TransactionResource.
 *
 * @mixin Transaction
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Transaction|MissingValue|null $transaction, Query $query)
    {
        parent::__construct($transaction, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->transaction_id),
            'date' => $this->when($this->isAllowedField('date'), $this->date),
            'service' => $this->when($this->isAllowedField('service'), $this->service->description),
            'description' => $this->when($this->isAllowedField('description'), $this->description),
            'amount' => $this->when($this->isAllowedField('amount'), $this->amount),
            'external_id' => $this->when($this->isAllowedField('external_id'), $this->external_id),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [];
    }
}
