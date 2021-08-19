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
 * @property int $theme_id
 * @property string|null $group
 * @property Enum|null $type
 * @property int|null $sequence
 * @property string $slug
 * @property int $anime_id
 * @property int|null $song_id
 * @property Anime $anime
 * @property Song|null $song
 * @property Collection $animethemeentries
 * @method static AnimeThemeFactory factory(...$parameters)
 */
class AnimeTheme extends BaseModel
{
    use CastsEnums;
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['type', 'sequence', 'group'];

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
    protected $table = 'anime_themes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'theme_id';

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['anime.animesynonyms', 'song']);
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
        'type' => ThemeType::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'int',
        'sequence' => 'int',
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
        return $this->belongsTo('App\Models\Wiki\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the song that the theme uses.
     *
     * @return BelongsTo
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Song', 'song_id', 'song_id');
    }

    /**
     * Get the entries for the theme.
     *
     * @return HasMany
     */
    public function animethemeentries(): HasMany
    {
        return $this->hasMany('App\Models\Wiki\Anime\Theme\AnimeThemeEntry', 'theme_id', 'theme_id');
    }
}