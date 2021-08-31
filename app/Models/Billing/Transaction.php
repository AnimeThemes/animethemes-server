<?php

declare(strict_types=1);

namespace App\Models\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\Service;
use App\Events\Billing\Transaction\TransactionCreated;
use App\Events\Billing\Transaction\TransactionDeleted;
use App\Events\Billing\Transaction\TransactionRestored;
use App\Events\Billing\Transaction\TransactionUpdated;
use App\Models\BaseModel;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Traits\CastsEnums;
use Carbon\Carbon;
use Database\Factories\Billing\TransactionFactory;
use Illuminate\Support\Str;

/**
 * Class Transaction.
 *
 * @property int $transaction_id
 * @property Carbon $date
 * @property Enum $service
 * @property string $description
 * @property float $amount
 * @property string|null $external_id
 * @method static TransactionFactory factory(...$parameters)
 */
class Transaction extends BaseModel
{
    use CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
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
    protected $table = 'transactions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'transaction_id';

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        'service' => Service::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'service' => 'int',
        'date' => 'date:Y-m-d',
        'amount' => 'decimal:2',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of($this->service->description)
            ->append(' ')
            ->append($this->date->format(AllowedDateFormat::YMD))
            ->__toString();
    }
}
