<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime;

use App\Concerns\Models\Reportable;
use App\Concerns\Models\SoftDeletes;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeDeleting;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use App\Scopes\WithoutInsertSongScope;
use Database\Factories\Wiki\Anime\AnimeThemeFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

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
class AnimeTheme extends BaseModel implements InteractsWithSchema, SoftDeletable
{
    use HasFactory;
    use Reportable;
    use Searchable;
    use SoftDeletes;

    final public const TABLE = 'anime_themes';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_ID = 'theme_id';
    final public const ATTRIBUTE_GROUP = 'group_id';
    final public const ATTRIBUTE_SEQUENCE = 'sequence';
    final public const ATTRIBUTE_SLUG = 'slug';
    final public const ATTRIBUTE_SONG = 'song_id';
    final public const ATTRIBUTE_TYPE = 'type';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_ARTISTS = 'song.artists';
    final public const RELATION_AUDIO = 'animethemeentries.videos.audio';
    final public const RELATION_ENTRIES = 'animethemeentries';
    final public const RELATION_GROUP = 'group';
    final public const RELATION_IMAGES = 'anime.images';
    final public const RELATION_PERFORMANCES = 'song.performances';
    final public const RELATION_PERFORMANCES_ARTISTS = 'song.performances.artist';
    final public const RELATION_SONG = 'song';
    final public const RELATION_SYNONYMS = 'anime.animesynonyms';
    final public const RELATION_VIDEOS = 'animethemeentries.videos';

    /**
     * The "boot" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        if (! Context::get('serving-graphql')) {
            static::addGlobalScope(new WithoutInsertSongScope);
        }
    }

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => ThemeCreated::class,
        'deleted' => ThemeDeleted::class,
        'deleting' => ThemeDeleting::class,
        'restored' => ThemeRestored::class,
        'updated' => ThemeUpdated::class,
    ];

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
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
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
     *
     * @return array
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
     * Gets the anime that owns the theme.
     *
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeTheme::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the group that the theme uses.
     *
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, AnimeTheme::ATTRIBUTE_GROUP);
    }

    /**
     * Gets the song that the theme uses.
     *
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, AnimeTheme::ATTRIBUTE_SONG);
    }

    /**
     * Get the entries for the theme.
     *
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
