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
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Balance::ATTRIBUTE_DATE)) {
            $result[Balance::ATTRIBUTE_DATE] = $this->date;
        }

        if ($this->isAllowedField(Balance::ATTRIBUTE_SERVICE)) {
            $result[Balance::ATTRIBUTE_SERVICE] = $this->service->description;
        }

        if ($this->isAllowedField(Balance::ATTRIBUTE_FREQUENCY)) {
            $result[Balance::ATTRIBUTE_FREQUENCY] = $this->frequency->description;
        }

        if ($this->isAllowedField(Balance::ATTRIBUTE_USAGE)) {
            $result[Balance::ATTRIBUTE_USAGE] = $this->usage;
        }

        if ($this->isAllowedField(BalanceResource::ATTRIBUTE_BALANCE)) {
            $result[BalanceResource::ATTRIBUTE_BALANCE] = $this->balance;
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
