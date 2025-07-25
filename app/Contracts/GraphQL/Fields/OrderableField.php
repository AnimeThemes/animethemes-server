<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\Enums\GraphQL\OrderType;

interface OrderableField
{
    /**
     * The order type of the field.
     */
    public function orderType(): OrderType;
}
