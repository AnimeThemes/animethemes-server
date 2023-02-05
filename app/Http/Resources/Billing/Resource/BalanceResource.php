<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Models\Billing\Balance;
use Illuminate\Http\Resources\MissingValue;

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
     * Create a new resource instance.
     *
     * @param  Balance | MissingValue | null  $balance
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Balance|MissingValue|null $balance, ReadQuery $query)
    {
        parent::__construct($balance, $query);
    }

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
