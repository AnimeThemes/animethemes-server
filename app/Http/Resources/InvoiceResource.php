<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceQuery;

class InvoiceResource extends BaseResource
{
    use PerformsResourceQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'invoice';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->invoice_id),
            'vendor' => $this->when($this->isAllowedField('vendor'), strval(optional($this->vendor)->description)),
            'description' => $this->when($this->isAllowedField('description'), $this->description),
            'amount' => $this->when($this->isAllowedField('amount'), $this->amount),
            'external_id' => $this->when($this->isAllowedField('external_id'), is_null($this->external_id) ? '' : $this->external_id),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
        ];
    }
}
