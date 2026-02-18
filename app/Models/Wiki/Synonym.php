<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\HasSynonyms;
use App\Contracts\Models\Nameable;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\SynonymType;
use App\Events\Wiki\Synonym\SynonymCreated;
use App\Events\Wiki\Synonym\SynonymDeleted;
use App\Events\Wiki\Synonym\SynonymRestored;
use App\Events\Wiki\Synonym\SynonymUpdated;
use App\Models\BaseModel;
use Database\Factories\Wiki\SynonymFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $synonym_id
 * @property Model&Nameable&HasSynonyms $synonymable
 * @property string $synonymable_type
 * @property int $synonymable_id
 * @property string $text
 * @property SynonymType $type
 *
 * @method static SynonymFactory factory(...$parameters)
 */
class Synonym extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'synonyms';

    final public const string ATTRIBUTE_ID = 'synonym_id';
    final public const string ATTRIBUTE_SYNONYMABLE_TYPE = 'synonymable_type';
    final public const string ATTRIBUTE_SYNONYMABLE_ID = 'synonymable_id';
    final public const string ATTRIBUTE_TEXT = 'text';
    final public const string ATTRIBUTE_TYPE = 'type';

    final public const string RELATION_SYNONYMABLE = 'synonymable';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Synonym::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Synonym::ATTRIBUTE_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Synonym::ATTRIBUTE_SYNONYMABLE_TYPE,
        Synonym::ATTRIBUTE_SYNONYMABLE_ID,
        Synonym::ATTRIBUTE_TEXT,
        Synonym::ATTRIBUTE_TYPE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => SynonymCreated::class,
        'deleted' => SynonymDeleted::class,
        'restored' => SynonymRestored::class,
        'updated' => SynonymUpdated::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Synonym::ATTRIBUTE_TYPE => SynonymType::class,
        ];
    }

    public function getName(): string
    {
        return $this->text;
    }

    public function getSubtitle(): string
    {
        return $this->synonymable->getName();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function synonymable(): MorphTo
    {
        return $this->morphTo();
    }
}
