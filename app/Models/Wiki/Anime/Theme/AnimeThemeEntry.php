<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime\Theme;

use App\Concerns\Models\Aggregate\AggregatesLike;
use App\Concerns\Models\InteractsWithLikes;
use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\HasAggregateLikes;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\Likeable;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Anime\Theme\Entry\EntryCreated;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleted;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleting;
use App\Events\Wiki\Anime\Theme\Entry\EntryRestored;
use App\Events\Wiki\Anime\Theme\Entry\EntryUpdated;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Models\BaseModel;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use App\Scopes\WithoutInsertSongScope;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme\EntryQuery;
use Database\Factories\Wiki\Anime\Theme\AnimeThemeEntryFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as ZnckBelongsToThrough;

/**
 * @property Anime $anime
 * @property AnimeTheme $animetheme
 * @property int $entry_id
 * @property string|null $episodes
 * @property string|null $notes
 * @property bool $nsfw
 * @property Collection<int, ExternalResource> $resources
 * @property bool $spoiler
 * @property int $theme_id
 * @property int|null $version
 * @property Collection<int, Video> $videos
 *
 * @method static AnimeThemeEntryFactory factory(...$parameters)
 */
class AnimeThemeEntry extends BaseModel implements HasAggregateLikes, HasResources, InteractsWithSchema, Likeable, SoftDeletable
{
    use AggregatesLike;
    use HasFactory;
    use InteractsWithLikes;
    use Reportable;
    use Searchable;
    use SoftDeletes;
    use ZnckBelongsToThrough;

    final public const string TABLE = 'anime_theme_entries';

    final public const string ATTRIBUTE_EPISODES = 'episodes';
    final public const string ATTRIBUTE_ID = 'entry_id';
    final public const string ATTRIBUTE_NOTES = 'notes';
    final public const string ATTRIBUTE_NSFW = 'nsfw';
    final public const string ATTRIBUTE_SPOILER = 'spoiler';
    final public const string ATTRIBUTE_THEME = 'theme_id';
    final public const string ATTRIBUTE_VERSION = 'version';

    final public const string RELATION_ANIME = 'animetheme.anime';
    final public const string RELATION_ANIME_SHALLOW = 'anime';
    final public const string RELATION_RESOURCES = 'resources';
    final public const string RELATION_SONG = 'animetheme.song';
    final public const string RELATION_SONG_SHALLOW = 'song';
    final public const string RELATION_SYNONYMS = 'animetheme.anime.animesynonyms';
    final public const string RELATION_THEME = 'animetheme';
    final public const string RELATION_THEME_GROUP = 'animetheme.group';
    final public const string RELATION_TRACKS = 'tracks';
    final public const string RELATION_VIDEOS = 'videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        AnimeThemeEntry::ATTRIBUTE_EPISODES,
        AnimeThemeEntry::ATTRIBUTE_NOTES,
        AnimeThemeEntry::ATTRIBUTE_NSFW,
        AnimeThemeEntry::ATTRIBUTE_SPOILER,
        AnimeThemeEntry::ATTRIBUTE_THEME,
        AnimeThemeEntry::ATTRIBUTE_VERSION,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => EntryCreated::class,
        'deleted' => EntryDeleted::class,
        'deleting' => EntryDeleting::class,
        'restored' => EntryRestored::class,
        'updated' => EntryUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeThemeEntry::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = AnimeThemeEntry::ATTRIBUTE_ID;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            AnimeThemeEntry::ATTRIBUTE_NSFW => 'boolean',
            AnimeThemeEntry::ATTRIBUTE_SPOILER => 'boolean',
            AnimeThemeEntry::ATTRIBUTE_VERSION => 'int',
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            AnimeThemeEntry::RELATION_SYNONYMS,
            AnimeThemeEntry::RELATION_SONG,
        ]);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $theme = is_null($this->animetheme)
            ? $this->animetheme()->withoutGlobalScope(WithoutInsertSongScope::class)->first()
            : $this->animetheme;

        $array['theme'] = $theme->toSearchableArray();

        // Overwrite version with readable format "v{#}"
        $array['version'] = Str::of(blank($this->version) ? '1' : $this->version)->prepend('v')->__toString();

        return $array;
    }

    public function getElasticQuery(): string
    {
        return EntryQuery::class;
    }

    public function getName(): string
    {
        $theme = is_null($this->animetheme)
            ? $this->animetheme()->withoutGlobalScope(WithoutInsertSongScope::class)->first()
            : $this->animetheme;

        if (is_null($theme)) {
            return strval($this->getKey());
        }

        return Str::of($this->anime->name)
            ->append(' ')
            ->append($theme->type->localize())
            ->when($theme->type !== ThemeType::IN, fn (Stringable $str) => $str->append(strval($theme->sequence ?? 1)))
            ->when(filled($this->version), fn (Stringable $str) => $str->append('v'.$this->version))
            ->when($theme->group !== null, fn (Stringable $str) => $str->append('-'.$theme->group->slug))
            ->__toString();
    }

    public function getSubtitle(): string
    {
        return "{$this->anime->getName()} {$this->animetheme->getName()}";
    }

    /**
     * @return BelongsTo<AnimeTheme, $this>
     */
    public function animetheme(): BelongsTo
    {
        return $this->belongsTo(AnimeTheme::class, AnimeThemeEntry::ATTRIBUTE_THEME);
    }

    /**
     * @return BelongsToMany<Video, $this, AnimeThemeEntryVideo>
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            AnimeThemeEntryVideo::class,
            AnimeThemeEntryVideo::ATTRIBUTE_ENTRY,
            AnimeThemeEntryVideo::ATTRIBUTE_VIDEO
        )
            ->using(AnimeThemeEntryVideo::class)
            ->as(AnimeThemeEntryVideoResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'entryresource'>
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('entryresource')
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
            AnimeTheme::class,
            null,
            '',
            [
                Song::class => Song::ATTRIBUTE_ID,
                AnimeTheme::class => AnimeTheme::ATTRIBUTE_ID,
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
            AnimeTheme::class,
            null,
            '',
            [
                Anime::class => Anime::ATTRIBUTE_ID,
                AnimeTheme::class => AnimeTheme::ATTRIBUTE_ID,
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
