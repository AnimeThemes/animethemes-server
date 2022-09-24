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
use Database\Factories\Billing\TransactionFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Transaction.
 *
 * @property float $amount
 * @property Carbon $date
 * @property string $description
 * @property string|null $external_id
 * @property Enum $service
 * @property int $transaction_id
 *
 * @method static TransactionFactory factory(...$parameters)
 */
class Transaction extends BaseModel
{
    use Actionable;

    final public const TABLE = 'transactions';

    final public const ATTRIBUTE_AMOUNT = 'amount';
    final public const ATTRIBUTE_DATE = 'date';
    final public const ATTRIBUTE_DESCRIPTION = 'description';
    final public const ATTRIBUTE_EXTERNAL_ID = 'external_id';
    final public const ATTRIBUTE_ID = 'transaction_id';
    final public const ATTRIBUTE_SERVICE = 'service';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Transaction::ATTRIBUTE_AMOUNT,
        Transaction::ATTRIBUTE_DATE,
        Transaction::ATTRIBUTE_DESCRIPTION,
        Transaction::ATTRIBUTE_EXTERNAL_ID,
        Transaction::ATTRIBUTE_SERVICE,
    ];

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
    protected $table = Transaction::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Transaction::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        Transaction::ATTRIBUTE_AMOUNT => 'decimal:2',
        Transaction::ATTRIBUTE_DATE => 'date:Y-m-d',
        Transaction::ATTRIBUTE_SERVICE => Service::class,
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
