<?php

namespace App\Models;

use App\Enums\BillingFrequency;
use App\Enums\BillingService;
use App\Events\Balance\BalanceCreated;
use App\Events\Balance\BalanceDeleted;
use App\Events\Balance\BalanceRestored;
use App\Events\Balance\BalanceUpdated;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Support\Str;

class Balance extends BaseModel
{
    use CastsEnums;

    /**
     * @var array
     */
    protected $fillable = ['date', 'service', 'frequency', 'usage', 'amount'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => BalanceCreated::class,
        'deleted' => BalanceDeleted::class,
        'restored' => BalanceRestored::class,
        'updated' => BalanceUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'balance';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'balance_id';

    /**
     * @var array
     */
    protected $enumCasts = [
        'service' => BillingService::class,
        'frequency' => BillingFrequency::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'service' => 'int',
        'frequency' => 'int',
        'date' => 'date:Y-m-d',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return Str::of($this->service->description)
            ->append(' ')
            ->append($this->date)
            ->__toString();
    }
}
