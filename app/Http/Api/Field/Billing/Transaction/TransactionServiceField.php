<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Transaction;

use App\Enums\Models\Billing\Service;
use App\Http\Api\Field\EnumField;
use App\Models\Billing\Transaction;

/**
 * Class TransactionServiceField.
 */
class TransactionServiceField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::ATTRIBUTE_SERVICE, Service::class);
    }
}
