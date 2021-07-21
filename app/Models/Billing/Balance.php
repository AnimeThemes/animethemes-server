<?php

declare(strict_types=1);

namespace App\Models\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Events\Billing\Balance\BalanceCreated;
use App\Events\Billing\Balance\BalanceDeleted;
use App\Events\Billing\Balance\BalanceRestored;
use App\Events\Billing\Balance\BalanceUpdated;
use App\Models\BaseModel;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Traits\CastsEnums;
use Carbon\Carbon;
use Database\Factories\Billing\BalanceFactory;
use Illuminate\Support\Str;

/**
 * Class Balance.
 *
 * @property int $balance_id
 * @property Carbon $date
 * @property Enum $service
 * @property Enum $frequency
 * @property float $usage
 * @property float $balance
 * @method static BalanceFactory factory(...$parameters)
 */
class Balance extends BaseModel
{
    use CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['date', 'service', 'frequency', 'usage', 'balance'];

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
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        'service' => Service::class,
        'frequency' => BalanceFrequency::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'service' => 'int',
        'frequency' => 'int',
        'date' => 'date:Y-m-d',
        'usage' => 'decimal:2'
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
            ->append($this->date->format(AllowedDateFormat::YM))
            ->__toString();
    }
}
