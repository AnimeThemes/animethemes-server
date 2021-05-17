<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceQuery;

class BalanceResource extends BaseResource
{
    use PerformsResourceQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'balance';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->balance_id),
            'date' => $this->when($this->isAllowedField('date'), $this->date),
            'service' => $this->when($this->isAllowedField('service'), strval(optional($this->service)->description)),
            'frequency' => $this->when($this->isAllowedField('frequency'), strval(optional($this->frequency)->description)),
            'usage' => $this->when($this->isAllowedField('usage'), $this->usage),
            'month_to_date_balance' => $this->when($this->isAllowedField('month_to_date_balance'), $this->balance),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
        ];
    }
}
