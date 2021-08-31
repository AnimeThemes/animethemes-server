<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime\Theme;

use App\Events\Wiki\Anime\Theme\Entry\EntryCreated;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleted;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleting;
use App\Events\Wiki\Anime\Theme\Entry\EntryRestored;
use App\Events\Wiki\Anime\Theme\Entry\EntryUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Pivots\AnimeThemeEntryVideo;
use Database\Factories\Wiki\Anime\Theme\AnimeThemeEntryFactory;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class AnimeThemeEntry.
 *
 * @property int $entry_id
 * @property int|null $version
 * @property string|null $episodes
 * @property bool $nsfw
 * @property bool $spoiler
 * @property string|null $notes
 * @property int $theme_id
 * @property AnimeTheme $animetheme
 * @property Collection $videos
 * @property Anime $anime
 * @method static AnimeThemeEntryFactory factory(...$parameters)
 */
class AnimeThemeEntry extends BaseModel
{
    use QueryDsl;
    use Searchable;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['version', 'episodes', 'nsfw', 'spoiler', 'notes'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
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
    protected $table = 'anime_theme_entries';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'entry_id';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'nsfw' => 'boolean',
        'spoiler' => 'boolean',
        'version' => 'int',
    ];

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['animetheme.anime.animesynonyms', 'animetheme.song']);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['theme'] = $this->animetheme->toSearchableArray();

        // Overwrite version with readable format "V{#}"
        $array['version'] = Str::of(empty($this->version) ? '1' : $this->version)->prepend('V')->__toString();

        return $array;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of($this->anime->name)
            ->append(' ')
            ->append($this->animetheme->slug)
            ->append(empty($this->version) ? '' : " V{$this->version}")
            ->__toString();
    }

    /**
     * Get the theme that owns the entry.
     *
     * @return BelongsTo
     */
    public function animetheme(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Anime\AnimeTheme', 'theme_id', 'theme_id');
    }

    /**
     * Get the videos linked in the theme entry.
     *
     * @return BelongsToMany
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Video', 'anime_theme_entry_video', 'entry_id', 'video_id')
            ->using(AnimeThemeEntryVideo::class)
            ->withTimestamps();
    }

    /**
     * Get the anime that owns the entry through the theme.
     *
     * @return BelongsToThrough
     */
    public function anime(): BelongsToThrough
    {
        return $this->belongsToThrough(
            'App\Models\Wiki\Anime',
            'App\Models\Wiki\Anime\AnimeTheme',
            null,
            '',
            ['App\Models\Wiki\Anime' => 'anime_id', 'App\Models\Wiki\Anime\AnimeTheme' => 'theme_id']
        );
    }
}
