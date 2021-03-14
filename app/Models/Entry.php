<?php

namespace App\Models;

use App\Events\Entry\EntryCreated;
use App\Events\Entry\EntryDeleted;
use App\Events\Entry\EntryDeleting;
use App\Events\Entry\EntryRestored;
use App\Events\Entry\EntryUpdated;
use App\Pivots\VideoEntry;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Entry extends BaseModel
{
    use QueryDsl, Searchable;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    /**
     * @var array
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
    protected $table = 'entry';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'entry_id';

    /**
     * @var array
     */
    protected $casts = [
        'nsfw' => 'boolean',
        'spoiler' => 'boolean',
        'version' => 'int',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['theme'] = optional($this->theme)->toSearchableArray();

        //Overwrite version with readable format "V{#}"
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
    public function getName()
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme()
    {
        return $this->belongsTo('App\Models\Theme', 'theme_id', 'theme_id');
    }

    /**
     * Get the videos linked in the theme entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function videos()
    {
        return $this->belongsToMany('App\Models\Video', 'entry_video', 'entry_id', 'video_id')->using(VideoEntry::class);
    }

    /**
     * Get the anime that owns the entry through the theme.
     *
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function anime()
    {
        return $this->belongsToThrough('App\Models\Anime', 'App\Models\Theme', null, '', ['App\Models\Anime' => 'anime_id', 'App\Models\Theme' => 'theme_id']);
    }
}
