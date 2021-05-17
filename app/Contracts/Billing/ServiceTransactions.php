<?php

namespace App\Contracts\Billing;

interface ServiceTransactions
{
    /**
     * Collect transactions from billing service.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTransactions();
}
