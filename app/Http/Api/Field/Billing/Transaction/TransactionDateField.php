<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Transaction;

use App\Http\Api\Field\DateField;
use App\Models\Billing\Transaction;

/**
 * Class TransactionDateField.
 */
class TransactionDateField extends DateField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::ATTRIBUTE_DATE);
    }
}
