<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query\ReadQuery;
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
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Transaction|MissingValue|null $transaction, ReadQuery $query)
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
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Transaction::ATTRIBUTE_DATE)) {
            $result[Transaction::ATTRIBUTE_DATE] = $this->date;
        }

        if ($this->isAllowedField(Transaction::ATTRIBUTE_SERVICE)) {
            $result[Transaction::ATTRIBUTE_SERVICE] = $this->service->description;
        }

        if ($this->isAllowedField(Transaction::ATTRIBUTE_DESCRIPTION)) {
            $result[Transaction::ATTRIBUTE_DESCRIPTION] = $this->description;
        }

        if ($this->isAllowedField(Transaction::ATTRIBUTE_AMOUNT)) {
            $result[Transaction::ATTRIBUTE_AMOUNT] = $this->amount;
        }

        if ($this->isAllowedField(Transaction::ATTRIBUTE_EXTERNAL_ID)) {
            $result[Transaction::ATTRIBUTE_EXTERNAL_ID] = $this->external_id;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        return $result;
    }
}
