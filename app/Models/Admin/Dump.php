<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpRestored;
use App\Events\Admin\Dump\DumpUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\DumpFactory;

/**
 * Class Dump.
 *
 * @property int $dump_id
 * @property string $path
 *
 * @method static DumpFactory factory(...$parameters)
 */
class Dump extends BaseModel
{
    final public const TABLE = 'dumps';

    final public const ATTRIBUTE_ID = 'dump_id';
    final public const ATTRIBUTE_PATH = 'path';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Dump::ATTRIBUTE_PATH,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => DumpCreated::class,
        'deleted' => DumpDeleted::class,
        'restored' => DumpRestored::class,
        'updated' => DumpUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Dump::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Dump::ATTRIBUTE_ID;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->path;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->getName();
    }
}
