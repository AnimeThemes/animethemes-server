<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\DumpFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $dump_id
 * @property string $path
 * @property bool $public
 *
 * @method static DumpFactory factory(...$parameters)
 * @method static Builder public()
 */
class Dump extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'dumps';

    final public const string ATTRIBUTE_ID = 'dump_id';
    final public const string ATTRIBUTE_PATH = 'path';
    final public const string ATTRIBUTE_PUBLIC = 'public';
    final public const string ATTRIBUTE_LINK = 'link';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => DumpCreated::class,
        'deleted' => DumpDeleted::class,
        'updated' => DumpUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Dump::ATTRIBUTE_PATH,
        Dump::ATTRIBUTE_PUBLIC,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        Dump::ATTRIBUTE_LINK,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Dump::ATTRIBUTE_PUBLIC => 'bool',
        ];
    }

    /**
     * The link of the dump.
     */
    protected function link(): Attribute
    {
        return Attribute::make(function (): ?string {
            if ($this->hasAttribute($this->getRouteKeyName()) && $this->exists) {
                return route('dump.show', $this);
            }

            return null;
        });
    }

    public function getName(): string
    {
        return $this->path;
    }

    public function getSubtitle(): string
    {
        return $this->getName();
    }

    /**
     * Scope a query to only include public dumps.
     */
    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where(Dump::ATTRIBUTE_PUBLIC, true);
    }
}
