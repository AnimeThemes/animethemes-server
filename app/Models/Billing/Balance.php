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
 * @property float $balance
 * @property int $balance_id
 * @property Carbon $date
 * @property Enum $frequency
 * @property Enum $service
 * @property float $usage
 *
 * @method static BalanceFactory factory(...$parameters)
 */
class Balance extends BaseModel
{
    use CastsEnums;

    public const TABLE = 'balances';

    public const ATTRIBUTE_BALANCE = 'balance';
    public const ATTRIBUTE_DATE = 'date';
    public const ATTRIBUTE_FREQUENCY = 'frequency';
    public const ATTRIBUTE_ID = 'balance_id';
    public const ATTRIBUTE_SERVICE = 'service';
    public const ATTRIBUTE_USAGE = 'usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Balance::ATTRIBUTE_BALANCE,
        Balance::ATTRIBUTE_DATE,
        Balance::ATTRIBUTE_FREQUENCY,
        Balance::ATTRIBUTE_SERVICE,
        Balance::ATTRIBUTE_USAGE,
    ];

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
    protected $table = Balance::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Balance::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        Balance::ATTRIBUTE_FREQUENCY => BalanceFrequency::class,
        Balance::ATTRIBUTE_SERVICE => Service::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        Balance::ATTRIBUTE_BALANCE => 'decimal:2',
        Balance::ATTRIBUTE_DATE => 'date:Y-m-d',
        Balance::ATTRIBUTE_FREQUENCY => 'int',
        Balance::ATTRIBUTE_SERVICE => 'int',
        Balance::ATTRIBUTE_USAGE => 'decimal:2',
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
