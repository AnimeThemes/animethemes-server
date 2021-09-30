<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\BaseModel;
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
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Transaction::ATTRIBUTE_DATE => $this->when($this->isAllowedField(Transaction::ATTRIBUTE_DATE), $this->date),
            Transaction::ATTRIBUTE_SERVICE => $this->when($this->isAllowedField(Transaction::ATTRIBUTE_SERVICE), $this->service->description),
            Transaction::ATTRIBUTE_DESCRIPTION => $this->when($this->isAllowedField(Transaction::ATTRIBUTE_DESCRIPTION), $this->description),
            Transaction::ATTRIBUTE_AMOUNT => $this->when($this->isAllowedField(Transaction::ATTRIBUTE_AMOUNT), $this->amount),
            Transaction::ATTRIBUTE_EXTERNAL_ID => $this->when($this->isAllowedField(Transaction::ATTRIBUTE_EXTERNAL_ID), $this->external_id),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
        ];
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
