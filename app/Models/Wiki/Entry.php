<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Entry\EntryCreated;
use App\Events\Wiki\Entry\EntryDeleted;
use App\Events\Wiki\Entry\EntryForceDeleting;
use App\Events\Wiki\Entry\EntryRestored;
use App\Events\Wiki\Entry\EntryUpdated;
use App\Http\Api\Schema\Wiki\EntrySchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoJsonResource;
use App\Models\BaseModel;
use App\Models\List\Playlist\PlaylistTrack;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\EntryVideo;
use App\Scout\Elasticsearch\Models\Wiki\EntryElasticModel;
use App\Scout\Typesense\Models\Wiki\EntryTypesenseModel;
use Database\Factories\Wiki\EntryFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use RuntimeException;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as ZnckBelongsToThrough;

/**
 * @property Anime $anime
 * @property Theme $animetheme
 * @property int $entry_id
 * @property string|null $episodes
 * @property int $likes_count
 * @property string|null $notes
 * @property bool $nsfw
 * @property Collection<int, ExternalResource> $resources
 * @property bool $spoiler
 * @property int $theme_id
 * @property int $tracks_count
 * @property int $version
 * @property Collection<int, Video> $videos
 *
 * @method static EntryFactory factory(...$parameters)
 */
#[Table(Entry::TABLE, Entry::ATTRIBUTE_ID)]
class Entry extends BaseModel implements Auditable, HasResources, InteractsWithSchema, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use ZnckBelongsToThrough;

    final public const string TABLE = 'entries';

    final public const string ATTRIBUTE_EPISODES = 'episodes';

    final public const string ATTRIBUTE_ID = 'entry_id';

    final public const string ATTRIBUTE_LIKES_COUNT = 'likes_count';

    final public const string ATTRIBUTE_NOTES = 'notes';

    final public const string ATTRIBUTE_NSFW = 'nsfw';

    final public const string ATTRIBUTE_SPOILER = 'spoiler';

    final public const string ATTRIBUTE_THEME = 'theme_id';

    final public const string ATTRIBUTE_TRACKS_COUNT = 'tracks_count';

    final public const string ATTRIBUTE_VERSION = 'version';

    final public const string RELATION_ANIME = 'animetheme.anime';

    final public const string RELATION_ANIME_SHALLOW = 'anime';

    final public const string RELATION_RESOURCES = 'resources';

    final public const string RELATION_SONG = 'animetheme.song';

    final public const string RELATION_SONG_SHALLOW = 'song';

    final public const string RELATION_SYNONYMS = 'animetheme.anime.synonyms';

    final public const string RELATION_THEME = 'animetheme';

    final public const string RELATION_THEME_GROUP = 'animetheme.group';

    final public const string RELATION_TRACKS = 'tracks';

    final public const string RELATION_VIDEOS = 'videos';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => EntryCreated::class,
        'deleted' => EntryDeleted::class,
        'forceDeleting' => EntryForceDeleting::class,
        'restored' => EntryRestored::class,
        'updated' => EntryUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Entry::ATTRIBUTE_EPISODES,
        Entry::ATTRIBUTE_LIKES_COUNT,
        Entry::ATTRIBUTE_NOTES,
        Entry::ATTRIBUTE_NSFW,
        Entry::ATTRIBUTE_SPOILER,
        Entry::ATTRIBUTE_THEME,
        Entry::ATTRIBUTE_TRACKS_COUNT,
        Entry::ATTRIBUTE_VERSION,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Entry::ATTRIBUTE_EPISODES => 'string',
            Entry::ATTRIBUTE_LIKES_COUNT => 'int',
            Entry::ATTRIBUTE_NOTES => 'string',
            Entry::ATTRIBUTE_NSFW => 'boolean',
            Entry::ATTRIBUTE_SPOILER => 'boolean',
            Entry::ATTRIBUTE_THEME => 'int',
            Entry::ATTRIBUTE_TRACKS_COUNT => 'int',
            Entry::ATTRIBUTE_VERSION => 'int',
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            Entry::RELATION_SYNONYMS,
            Entry::RELATION_SONG,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return match ($driver = Config::get('scout.driver')) {
            'collection',
            'elastic' => EntryElasticModel::toSearchableArray($this),
            'typesense' => EntryTypesenseModel::toSearchableArray($this),
            default => throw new RuntimeException("Unsupported {$driver} search driver configured."),
        };
    }

    public function getName(): string
    {
        $theme = $this->animetheme;

        return Str::of($this->anime->title)
            ->append(' ')
            ->append($theme->type->localize())
            ->when($theme->type !== ThemeType::IN, fn (Stringable $str) => $str->append(strval($theme->sequence ?? 1)))
            ->when($this->version !== 1, fn (Stringable $str) => $str->append('v'.$this->version))
            ->when($theme->group !== null, fn (Stringable $str) => $str->append('-'.$theme->group->slug))
            ->__toString();
    }

    public function getSubtitle(): string
    {
        return "{$this->anime->getName()} {$this->animetheme->getName()}";
    }

    /**
     * @return BelongsTo<Theme, $this>
     */
    public function animetheme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, Entry::ATTRIBUTE_THEME);
    }

    /**
     * @return BelongsToMany<Video, $this, EntryVideo>
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            EntryVideo::class,
            EntryVideo::ATTRIBUTE_ENTRY,
            EntryVideo::ATTRIBUTE_VIDEO
        )
            ->using(EntryVideo::class)
            ->as(AnimeThemeEntryVideoJsonResource::$wrap)
            ->withPivot([EntryVideo::ATTRIBUTE_ID])
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'entryresource'>
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->as('entryresource')
            ->withPivot([Resourceable::ATTRIBUTE_ID, Resourceable::ATTRIBUTE_AS])
            ->withTimestamps();
    }

    /**
     * @return HasMany<PlaylistTrack, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class, PlaylistTrack::ATTRIBUTE_ENTRY);
    }

    /**
     * Get the song that owns the entry through the theme.
     */
    public function song(): BelongsToThrough
    {
        return $this->belongsToThrough(
            Song::class,
            Theme::class,
            null,
            '',
            [
                Song::class => Song::ATTRIBUTE_ID,
                Theme::class => Theme::ATTRIBUTE_ID,
            ]
        );
    }

    /**
     * Get the anime that owns the entry through the theme.
     */
    public function anime(): BelongsToThrough
    {
        return $this->belongsToThrough(
            Anime::class,
            Theme::class,
            null,
            '',
            [
                Anime::class => Anime::ATTRIBUTE_ID,
                Theme::class => Theme::ATTRIBUTE_ID,
            ]
        );
    }

    /**
     * Get the schema for the model.
     */
    public function schema(): EntrySchema
    {
        return new EntrySchema();
    }
}
