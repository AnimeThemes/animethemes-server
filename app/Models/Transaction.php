<?php

namespace App\Models;

use App\Enums\BillingService;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionDeleted;
use App\Events\Transaction\TransactionRestored;
use App\Events\Transaction\TransactionUpdated;
use BenSampo\Enum\Traits\CastsEnums;

class Transaction extends BaseModel
{
    use CastsEnums;

    /**
     * @var array
     */
    protected $fillable = ['date', 'service', 'description', 'amount', 'external_id'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TransactionCreated::class,
        'deleted' => TransactionDeleted::class,
        'restored' => TransactionRestored::class,
        'updated' => TransactionUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'transaction_id';

    /**
     * @var array
     */
    protected $enumCasts = [
        'service' => BillingService::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'service' => 'int',
        'date' => 'date:Y-m-d',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->description;
    }
}
