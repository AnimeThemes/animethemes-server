<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\DumpFactory;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $dump_id
 * @property string $path
 *
 * @method static DumpFactory factory(...$parameters)
 */
#[Appends([Dump::ATTRIBUTE_LINK])]
#[Table(Dump::TABLE, Dump::ATTRIBUTE_ID)]
class Dump extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'dumps';

    final public const string ATTRIBUTE_ID = 'dump_id';
    final public const string ATTRIBUTE_PATH = 'path';
    final public const string ATTRIBUTE_LINK = 'link';

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
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Dump::ATTRIBUTE_PATH => 'string',
        ];
    }

    /**
     * The link of the dump.
     */
    protected function link(): Attribute
    {
        return Attribute::make(function (): ?string {
            if ($this->getAttribute($this->getRouteKeyName()) !== null && $this->exists) {
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
}
