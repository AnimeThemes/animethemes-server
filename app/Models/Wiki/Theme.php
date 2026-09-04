<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Theme\ThemeCreated;
use App\Events\Wiki\Theme\ThemeDeleted;
use App\Events\Wiki\Theme\ThemeDeleting;
use App\Events\Wiki\Theme\ThemeRestored;
use App\Events\Wiki\Theme\ThemeUpdated;
use App\Http\Api\Schema\Wiki\ThemeSchema;
use App\Models\BaseModel;
use App\Observers\Wiki\ThemeObserver;
use App\Scopes\WithoutInsertSongScope;
use App\Scout\Elasticsearch\Models\Wiki\ThemeElasticModel;
use App\Scout\Typesense\Models\Wiki\ThemeTypesenseModel;
use Database\Factories\Wiki\ThemeFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use RuntimeException;

/**
 * @property Anime $anime
 * @property int $anime_id
 * @property Collection<int, Entry> $animethemeentries
 * @property Group|null $group
 * @property int|null $group_id
 * @property int|null $sequence
 * @property string $slug
 * @property Song|null $song
 * @property int|null $song_id
 * @property int $theme_id
 * @property ThemeType $type
 *
 * @method static ThemeFactory factory(...$parameters)
 */
#[ObservedBy(ThemeObserver::class)]
#[ScopedBy(WithoutInsertSongScope::class)]
#[Table(Theme::TABLE, Theme::ATTRIBUTE_ID)]
class Theme extends BaseModel implements Auditable, InteractsWithSchema, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    final public const string TABLE = 'themes';

    final public const string ATTRIBUTE_ANIME = 'anime_id';

    final public const string ATTRIBUTE_ID = 'theme_id';

    final public const string ATTRIBUTE_GROUP = 'group_id';

    final public const string ATTRIBUTE_SEQUENCE = 'sequence';

    final public const string ATTRIBUTE_SLUG = 'slug';

    final public const string ATTRIBUTE_SONG = 'song_id';

    final public const string ATTRIBUTE_TYPE = 'type';

    final public const string RELATION_ANIME = 'anime';

    final public const string RELATION_ARTISTS = 'song.artists';

    final public const string RELATION_AUDIO = 'animethemeentries.videos.audio';

    final public const string RELATION_ENTRIES = 'animethemeentries';

    final public const string RELATION_GROUP = 'group';

    final public const string RELATION_IMAGES = 'anime.images';

    final public const string RELATION_PERFORMANCES = 'song.performances';

    final public const string RELATION_PERFORMANCES_ARTISTS = 'song.performances.artist';

    final public const string RELATION_SONG = 'song';

    final public const string RELATION_SYNONYMS = 'anime.synonyms';

    final public const string RELATION_VIDEOS = 'animethemeentries.videos';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ThemeCreated::class,
        'deleted' => ThemeDeleted::class,
        'deleting' => ThemeDeleting::class,
        'restored' => ThemeRestored::class,
        'updated' => ThemeUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Theme::ATTRIBUTE_ANIME,
        Theme::ATTRIBUTE_GROUP,
        Theme::ATTRIBUTE_SEQUENCE,
        Theme::ATTRIBUTE_SLUG,
        Theme::ATTRIBUTE_SONG,
        Theme::ATTRIBUTE_TYPE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Theme::ATTRIBUTE_ANIME => 'int',
            Theme::ATTRIBUTE_GROUP => 'int',
            Theme::ATTRIBUTE_SEQUENCE => 'int',
            Theme::ATTRIBUTE_SLUG => 'string',
            Theme::ATTRIBUTE_SONG => 'int',
            Theme::ATTRIBUTE_TYPE => ThemeType::class,
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            Theme::RELATION_SYNONYMS,
            Theme::RELATION_SONG,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return match ($driver = Config::get('scout.driver')) {
            'collection',
            'elastic' => ThemeElasticModel::toSearchableArray($this),
            'typesense' => ThemeTypesenseModel::toSearchableArray($this),
            default => throw new RuntimeException("Unsupported {$driver} search driver configured."),
        };
    }

    public function getName(): string
    {
        return Str::of($this->anime->getName())
            ->append(' ')
            ->append($this->type->localize())
            ->when($this->type === ThemeType::IN && $this->song !== null, fn (Stringable $str) => $str->append(" \"{$this->song->getName()}\" "))
            ->when($this->type !== ThemeType::IN, fn (Stringable $str) => $str->append(strval($this->sequence ?? 1)))
            ->when($this->group !== null, fn (Stringable $str) => $str->append('-'.$this->group->slug))
            ->trim()
            ->__toString();
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
        return $this->belongsTo(Anime::class, Theme::ATTRIBUTE_ANIME);
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, Theme::ATTRIBUTE_GROUP);
    }

    /**
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, Theme::ATTRIBUTE_SONG);
    }

    /**
     * @return HasMany<Entry, $this>
     */
    public function animethemeentries(): HasMany
    {
        return $this->hasMany(Entry::class, Entry::ATTRIBUTE_THEME);
    }

    /**
     * Get the schema for the model.
     */
    public function schema(): ThemeSchema
    {
        return new ThemeSchema();
    }
}
