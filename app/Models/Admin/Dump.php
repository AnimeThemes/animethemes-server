<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\DumpFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Dump.
 *
 * @property int $dump_id
 * @property string $path
 *
 * @method static DumpFactory factory(...$parameters)
 * @method static Builder onlySafeDumps()
 */
class Dump extends BaseModel
{
    use HasFactory;

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
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => DumpCreated::class,
        'deleted' => DumpDeleted::class,
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
     */
    public function getName(): string
    {
        return $this->path;
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        return $this->getName();
    }

    /**
     * Get the available safe dumps.
     *
     * @return string[]
     */
    public static function safeDumps(): array
    {
        return [
            DumpDocumentAction::FILENAME_PREFIX,
            DumpWikiAction::FILENAME_PREFIX,
        ];
    }

    /**
     * Scope a query to only include safe dumps.
     *
     * @param  Builder  $query
     */
    #[Scope]
    public function onlySafeDumps(Builder $query): void
    {
        $query->where(function (Builder $query) {
            foreach (Dump::safeDumps() as $path) {
                $query->orWhere(Dump::ATTRIBUTE_PATH, ComparisonOperator::LIKE->value, $path.'%');
            }
        });
    }
}
