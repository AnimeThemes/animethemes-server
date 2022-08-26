<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Contracts\Models\Nameable;
use App\Events\Admin\Setting\SettingCreated;
use App\Events\Admin\Setting\SettingDeleted;
use App\Events\Admin\Setting\SettingUpdated;
use Database\Factories\Admin\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Setting.
 *
 * @property int $id
 * @property string $key
 * @property string $value
 *
 * @method static SettingFactory factory(...$parameters)
 */
class Setting extends Model implements Auditable, Nameable
{
    use Actionable;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    final public const TABLE = 'settings';

    final public const ATTRIBUTE_KEY = 'key';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_VALUE = 'value';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Setting::ATTRIBUTE_KEY,
        Setting::ATTRIBUTE_VALUE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => SettingCreated::class,
        'deleted' => SettingDeleted::class,
        'updated' => SettingUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Setting::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Setting::ATTRIBUTE_ID;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->key;
    }
}
