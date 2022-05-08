<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Http\Request;

/**
 * Class BalanceCollection.
 */
class BalanceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'balances';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (Balance $balance) => new BalanceResource($balance, $this->query))->all();
    }
}
