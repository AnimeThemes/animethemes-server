<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class BalanceResource.
 */
class BalanceResource extends BaseResource
{
    final public const ATTRIBUTE_BALANCE = 'month_to_date_balance';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'balance';

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new BalanceSchema();
    }
}
