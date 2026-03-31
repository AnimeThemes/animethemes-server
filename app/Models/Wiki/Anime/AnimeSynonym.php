<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Models\Wiki\Anime\AnimeSynonymElasticModel;
use App\Scout\Typesense\Models\Wiki\Anime\AnimeSynonymTypesenseModel;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use RuntimeException;

/**
 * @property Anime $anime
 * @property int $anime_id
 * @property int $synonym_id
 * @property string|null $text
 * @property AnimeSynonymType $type
 *
 * @deprecated Use Synonym instead.
 */
#[Table(AnimeSynonym::TABLE, AnimeSynonym::ATTRIBUTE_ID)]
class AnimeSynonym extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'anime_synonyms';

    final public const string ATTRIBUTE_ANIME = 'anime_id';
    final public const string ATTRIBUTE_ID = 'synonym_id';
    final public const string ATTRIBUTE_TEXT = 'text';
    final public const string ATTRIBUTE_TYPE = 'type';

    final public const string RELATION_ANIME = 'anime';
    final public const string RELATION_SERIES = 'anime.series';
    final public const string RELATION_VIDEOS = 'anime.animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        AnimeSynonym::ATTRIBUTE_ANIME,
        AnimeSynonym::ATTRIBUTE_TEXT,
        AnimeSynonym::ATTRIBUTE_TYPE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            AnimeSynonym::ATTRIBUTE_ANIME => 'int',
            AnimeSynonym::ATTRIBUTE_TEXT => 'string',
            AnimeSynonym::ATTRIBUTE_TYPE => AnimeSynonymType::class,
        ];
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return match ($driver = Config::get('scout.driver')) {
            'collection',
            'elastic' => AnimeSynonymElasticModel::toSearchableArray($this),
            'typesense' => AnimeSynonymTypesenseModel::toSearchableArray($this),
            default => throw new RuntimeException("Unsupported {$driver} search driver configured."),
        };
    }

    public function getName(): string
    {
        return $this->text;
    }

    public function getSubtitle(): string
    {
        return $this->anime->getName();
    }

    /**
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeSynonym::ATTRIBUTE_ANIME);
    }
}
