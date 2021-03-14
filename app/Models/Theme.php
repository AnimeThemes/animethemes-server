<?php

namespace App\Models;

use App\Enums\ThemeType;
use App\Events\Theme\ThemeCreated;
use App\Events\Theme\ThemeCreating;
use App\Events\Theme\ThemeDeleted;
use App\Events\Theme\ThemeDeleting;
use App\Events\Theme\ThemeRestored;
use App\Events\Theme\ThemeUpdated;
use BenSampo\Enum\Traits\CastsEnums;
use ElasticScoutDriverPlus\QueryDsl;
use Laravel\Scout\Searchable;

class Theme extends BaseModel
{
    use CastsEnums, QueryDsl, Searchable;

    /**
     * @var array
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
    protected $table = 'theme';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'theme_id';

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['anime'] = optional($this->anime)->toSearchableArray();
        $array['song'] = optional($this->song)->toSearchableArray();

        return $array;
    }

    /**
     * @var array
     */
    protected $enumCasts = [
        'type' => ThemeType::class,
    ];

    /**
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
    public function getName()
    {
        return $this->slug;
    }

    /**
     * Gets the anime that owns the theme.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anime()
    {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the song that the theme uses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function song()
    {
        return $this->belongsTo('App\Models\Song', 'song_id', 'song_id');
    }

    /**
     * Get the entries for the theme.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany('App\Models\Entry', 'theme_id', 'theme_id');
    }
}
