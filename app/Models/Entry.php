<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\Entry\EntryCreated;
use App\Events\Entry\EntryDeleted;
use App\Events\Entry\EntryDeleting;
use App\Events\Entry\EntryRestored;
use App\Events\Entry\EntryUpdated;
use App\Pivots\VideoEntry;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class Entry.
 */
class Entry extends BaseModel
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
     * @var array<string, string>
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
    protected $table = 'entry';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'entry_id';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
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
        return $query->with(['theme.anime.synonyms', 'theme.song']);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['theme'] = $this->theme->toSearchableArray();

        // Overwrite version with readable format "V{#}"
        if (! empty($this->version)) {
            $array['version'] = Str::of($this->version)->trim()->prepend('V')->__toString();
        }

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
            ->append($this->theme->slug)
            ->append(empty($this->version) ? '' : " V{$this->version}")
            ->__toString();
    }

    /**
     * Get the theme that owns the entry.
     *
     * @return BelongsTo
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo('App\Models\Theme', 'theme_id', 'theme_id');
    }

    /**
     * Get the videos linked in the theme entry.
     *
     * @return BelongsToMany
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Video', 'entry_video', 'entry_id', 'video_id')
            ->using(VideoEntry::class)
            ->withTimestamps();
    }

    /**
     * Get the anime that owns the entry through the theme.
     *
     * @return BelongsToThrough
     */
    public function anime(): BelongsToThrough
    {
        return $this->belongsToThrough('App\Models\Anime', 'App\Models\Theme', null, '', ['App\Models\Anime' => 'anime_id', 'App\Models\Theme' => 'theme_id']);
    }
}
