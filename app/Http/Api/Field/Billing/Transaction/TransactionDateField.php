<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Transaction;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Http\Api\Field\DateField;
use App\Models\Billing\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class TransactionDateField.
 */
class TransactionDateField extends DateField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::ATTRIBUTE_DATE);
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
            Str::of('date_format:')->append(AllowedDateFormat::YMD)->__toString(),
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
            Str::of('date_format:')->append(AllowedDateFormat::YMD)->__toString(),
        ];
    }
}
