<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Http\Api\Field\EnumField;
use App\Models\Billing\Balance;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;

/**
 * Class BalanceFrequencyField.
 */
class BalanceFrequencyField extends EnumField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Balance::ATTRIBUTE_FREQUENCY, BalanceFrequency::class);
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
            new EnumValue(BalanceFrequency::class),
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
            new EnumValue(BalanceFrequency::class),
        ];
    }
}
