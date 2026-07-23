<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\HasSynonyms;
use App\Contracts\Models\Nameable;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Synonym\SynonymCreated;
use App\Events\Wiki\Synonym\SynonymDeleted;
use App\Events\Wiki\Synonym\SynonymRestored;
use App\Events\Wiki\Synonym\SynonymUpdated;
use App\Models\BaseModel;
use Database\Factories\Wiki\SynonymFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string|null $language
 * @property int $synonym_id
 * @property Model&Nameable&HasSynonyms $synonymable
 * @property string $synonymable_type
 * @property int $synonymable_id
 * @property string $text
 *
 * @method static SynonymFactory factory(...$parameters)
 */
#[Table(Synonym::TABLE, Synonym::ATTRIBUTE_ID)]
class Synonym extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;

    final public const string TABLE = 'synonyms';

    final public const string ATTRIBUTE_ID = 'synonym_id';
    final public const string ATTRIBUTE_LANGUAGE = 'language';
    final public const string ATTRIBUTE_SYNONYMABLE_TYPE = 'synonymable_type';
    final public const string ATTRIBUTE_SYNONYMABLE_ID = 'synonymable_id';
    final public const string ATTRIBUTE_TEXT = 'text';

    final public const string RELATION_SYNONYMABLE = 'synonymable';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Synonym::ATTRIBUTE_LANGUAGE,
        Synonym::ATTRIBUTE_SYNONYMABLE_TYPE,
        Synonym::ATTRIBUTE_SYNONYMABLE_ID,
        Synonym::ATTRIBUTE_TEXT,
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
            Synonym::ATTRIBUTE_LANGUAGE => 'string',
            Synonym::ATTRIBUTE_SYNONYMABLE_TYPE => 'string',
            Synonym::ATTRIBUTE_SYNONYMABLE_ID => 'int',
            Synonym::ATTRIBUTE_TEXT => 'string',
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
    public function anime(): MorphTo
    {
        return $this->synonymable();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function synonymable(): MorphTo
    {
        return $this->morphTo();
    }
}
