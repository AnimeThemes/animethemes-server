<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Transaction;

use App\Http\Api\Field\StringField;
use App\Models\Billing\Transaction;

/**
 * Class TransactionDescriptionField.
 */
class TransactionDescriptionField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::ATTRIBUTE_DESCRIPTION);
    }
}