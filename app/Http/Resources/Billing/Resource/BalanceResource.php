<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Models\BaseModel;
use App\Models\Billing\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class BalanceResource.
 *
 * @mixin Balance
 */
class BalanceResource extends BaseResource
{
    final public const ATTRIBUTE_BALANCE = 'month_to_date_balance';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'balance';

    /**
     * Create a new resource instance.
     *
     * @param  Balance | MissingValue | null  $balance
     * @param  Query  $query
     * @return void
     */
    public function __construct(Balance|MissingValue|null $balance, Query $query)
    {
        parent::__construct($balance, $query);
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
            Balance::ATTRIBUTE_DATE => $this->when($this->isAllowedField(Balance::ATTRIBUTE_DATE), $this->date),
            Balance::ATTRIBUTE_SERVICE => $this->when($this->isAllowedField(Balance::ATTRIBUTE_SERVICE), $this->service->description),
            Balance::ATTRIBUTE_FREQUENCY => $this->when($this->isAllowedField(Balance::ATTRIBUTE_FREQUENCY), $this->frequency->description),
            Balance::ATTRIBUTE_USAGE => $this->when($this->isAllowedField(Balance::ATTRIBUTE_USAGE), $this->usage),
            BalanceResource::ATTRIBUTE_BALANCE => $this->when($this->isAllowedField(BalanceResource::ATTRIBUTE_BALANCE), $this->balance),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
        ];
    }
}
