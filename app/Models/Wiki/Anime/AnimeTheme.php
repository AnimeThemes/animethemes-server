<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeDeleting;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Middleware\GraphQL\SetServingGraphQL;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use App\Observers\Wiki\Anime\AnimeThemeObserver;
use App\Scopes\WithoutInsertSongScope;
use Database\Factories\Wiki\Anime\AnimeThemeFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property Anime $anime
 * @property int $anime_id
 * @property Collection<int, AnimeThemeEntry> $animethemeentries
 * @property Group|null $group
 * @property int|null $group_id
 * @property int|null $sequence
 * @property string $slug
 * @property Song|null $song
 * @property int|null $song_id
 * @property int $theme_id
 * @property ThemeType|null $type
 *
 * @method static AnimeThemeFactory factory(...$parameters)
 */
#[ObservedBy(AnimeThemeObserver::class)]
class AnimeTheme extends BaseModel implements Auditable, InteractsWithSchema, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'anime_themes';

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
    final public const string RELATION_SYNONYMS = 'anime.animesynonyms';
    final public const string RELATION_VIDEOS = 'animethemeentries.videos';

    /**
     * The "boot" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        if (! SetServingGraphQL::$isServing) {
            static::addGlobalScope(new WithoutInsertSongScope);
        }
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeTheme::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = AnimeTheme::ATTRIBUTE_ID;

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
        AnimeTheme::ATTRIBUTE_ANIME,
        AnimeTheme::ATTRIBUTE_GROUP,
        AnimeTheme::ATTRIBUTE_SEQUENCE,
        AnimeTheme::ATTRIBUTE_SLUG,
        AnimeTheme::ATTRIBUTE_SONG,
        AnimeTheme::ATTRIBUTE_TYPE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            AnimeTheme::ATTRIBUTE_SEQUENCE => 'int',
            AnimeTheme::ATTRIBUTE_SONG => 'int',
            AnimeTheme::ATTRIBUTE_TYPE => ThemeType::class,
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            AnimeTheme::RELATION_GROUP,
            AnimeTheme::RELATION_SYNONYMS,
            AnimeTheme::RELATION_SONG,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['anime'] = $this->anime->toSearchableArray();
        if ($this->song !== null) {
            $array['song'] = $this->song->toSearchableArray() + ['title_keyword' => $this->song->title];
        }

        return $array;
    }

    public function getName(): string
    {
        return Str::of($this->type->localize())
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
        return $this->belongsTo(Anime::class, AnimeTheme::ATTRIBUTE_ANIME);
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, AnimeTheme::ATTRIBUTE_GROUP);
    }

    /**
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, AnimeTheme::ATTRIBUTE_SONG);
    }

    /**
     * @return HasMany<AnimeThemeEntry, $this>
     */
    public function animethemeentries(): HasMany
    {
        return $this->hasMany(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_THEME);
    }

    /**
     * Get the schema for the model.
     */
    public function schema(): ThemeSchema
    {
        return new ThemeSchema();
    }
}
