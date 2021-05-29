<?php

namespace App\Models\Billing;

use App\Enums\Billing\Service;
use App\Enums\Filter\AllowedDateFormat;
use App\Events\Billing\Transaction\TransactionCreated;
use App\Events\Billing\Transaction\TransactionDeleted;
use App\Events\Billing\Transaction\TransactionRestored;
use App\Events\Billing\Transaction\TransactionUpdated;
use App\Models\BaseModel;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Support\Str;

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
        'service' => Service::class,
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
        return Str::of($this->service->description)
            ->append(' ')
            ->append($this->date->format(AllowedDateFormat::WITH_DAY))
            ->__toString();
    }
}
