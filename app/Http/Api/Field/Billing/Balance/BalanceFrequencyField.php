<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Billing\Balance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

/**
 * Class BalanceFrequencyField.
 */
class BalanceFrequencyField extends EnumField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Balance::ATTRIBUTE_FREQUENCY, BalanceFrequency::class);
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
            new Enum(BalanceFrequency::class),
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
            new Enum(BalanceFrequency::class),
        ];
    }
}
