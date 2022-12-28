<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\FloatField;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Http\Request;

/**
 * Class BalanceMonthToDateField.
 */
class BalanceMonthToDateField extends FloatField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(BalanceResource::ATTRIBUTE_BALANCE, Balance::ATTRIBUTE_BALANCE);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'decimal:0,2',
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'decimal:0,2',
        ];
    }
}
