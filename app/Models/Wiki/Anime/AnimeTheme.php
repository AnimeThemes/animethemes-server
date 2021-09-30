<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeCreating;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeDeleting;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Song;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Traits\CastsEnums;
use Database\Factories\Wiki\Anime\AnimeThemeFactory;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class AnimeTheme.
 *
 * @property Anime $anime
 * @property int $anime_id
 * @property Collection $animethemeentries
 * @property string|null $group
 * @property int|null $sequence
 * @property string $slug
 * @property Song|null $song
 * @property int|null $song_id
 * @property int $theme_id
 * @property Enum|null $type
 *
 * @method static AnimeThemeFactory factory(...$parameters)
 */
class AnimeTheme extends BaseModel
{
    use CastsEnums;
    use QueryDsl;
    use Searchable;

    public const TABLE = 'anime_themes';

    public const ATTRIBUTE_ANIME = 'anime_id';
    public const ATTRIBUTE_GROUP = 'group';
    public const ATTRIBUTE_ID = 'theme_id';
    public const ATTRIBUTE_SEQUENCE = 'sequence';
    public const ATTRIBUTE_SLUG = 'slug';
    public const ATTRIBUTE_SONG = 'song_id';
    public const ATTRIBUTE_TYPE = 'type';

    public const RELATION_ANIME = 'anime';
    public const RELATION_ARTISTS = 'song.artists';
    public const RELATION_ENTRIES = 'animethemeentries';
    public const RELATION_IMAGES = 'anime.images';
    public const RELATION_SONG = 'song';
    public const RELATION_SYNONYMS = 'anime.animesynonyms';
    public const RELATION_VIDEOS = 'animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        AnimeTheme::ATTRIBUTE_GROUP,
        AnimeTheme::ATTRIBUTE_SEQUENCE,
        AnimeTheme::ATTRIBUTE_TYPE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ThemeCreated::class,
        'creating' => ThemeCreating::class,
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
        $array['song'] = $this->song?->toSearchableArray();

        return $array;
    }

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array
     */
    protected $enumCasts = [
        AnimeTheme::ATTRIBUTE_TYPE => ThemeType::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        AnimeTheme::ATTRIBUTE_SEQUENCE => 'int',
        AnimeTheme::ATTRIBUTE_TYPE => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->slug;
    }

    /**
     * Gets the anime that owns the theme.
     *
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeTheme::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the song that the theme uses.
     *
     * @return BelongsTo
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, AnimeTheme::ATTRIBUTE_SONG);
    }

    /**
     * Get the entries for the theme.
     *
     * @return HasMany
     */
    public function animethemeentries(): HasMany
    {
        return $this->hasMany(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_THEME);
    }
}
