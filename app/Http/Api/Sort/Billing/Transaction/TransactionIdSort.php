<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Billing\Transaction;

use App\Http\Api\Sort\Sort;
use App\Models\Billing\Transaction;
use Illuminate\Support\Collection;

/**
 * Class TransactionIdSort.
 */
class TransactionIdSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'id');
    }

    /**
     * Get sort column.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getColumn(): string
    {
        return (new Transaction())->getKeyName();
    }
}
