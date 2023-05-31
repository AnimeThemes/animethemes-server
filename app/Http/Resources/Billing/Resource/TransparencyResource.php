<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Resource;

use App\Models\Billing\Balance;
use App\Models\Billing\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class TransparencyResource.
 */
class TransparencyResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'transparency';

    /**
     * Create a new resource instance.
     *
     * @param  Carbon|null  $selectedDate
     * @param  Collection<int, Carbon>  $filterOptions
     * @param  Collection<int, Balance>  $balances
     * @param  Collection<int, Transaction>  $transactions
     * @return void
     */
    public function __construct(
        protected readonly ?Carbon $selectedDate,
        protected readonly Collection $filterOptions,
        protected readonly Collection $balances,
        protected readonly Collection $transactions
    ) {
        parent::__construct(new MissingValue());
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return [
            'selectedDate' => $this->selectedDate,
            'filterOptions' => $this->filterOptions,
            'balances' => $this->balances,
            'transactions' => $this->transactions,
        ];
    }
}
