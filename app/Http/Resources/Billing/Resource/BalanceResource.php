<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
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
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'balance';

    /**
     * Create a new resource instance.
     *
     * @param Balance | MissingValue | null $balance
     * @param Query $query
     * @return void
     */
    public function __construct(Balance | MissingValue | null $balance, Query $query)
    {
        parent::__construct($balance, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->balance_id),
            'date' => $this->when($this->isAllowedField('date'), $this->date),
            'service' => $this->when($this->isAllowedField('service'), $this->service->description),
            'frequency' => $this->when($this->isAllowedField('frequency'), $this->frequency->description),
            'usage' => $this->when($this->isAllowedField('usage'), $this->usage),
            'month_to_date_balance' => $this->when($this->isAllowedField('month_to_date_balance'), $this->balance),
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
