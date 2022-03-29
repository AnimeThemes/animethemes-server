<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Transaction;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Models\Billing\Transaction;
use Illuminate\Http\Request;

/**
 * Class TransactionDescriptionField.
 */
class TransactionDescriptionField extends StringField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::ATTRIBUTE_DESCRIPTION);
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
            'string',
            'max:192',
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
            'string',
            'max:192',
        ];
    }
}
