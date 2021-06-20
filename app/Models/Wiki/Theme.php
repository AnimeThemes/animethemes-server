<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Enums\Models\Wiki\ThemeType;
use App\Events\Wiki\Theme\ThemeCreated;
use App\Events\Wiki\Theme\ThemeCreating;
use App\Events\Wiki\Theme\ThemeDeleted;
use App\Events\Wiki\Theme\ThemeDeleting;
use App\Events\Wiki\Theme\ThemeRestored;
use App\Events\Wiki\Theme\ThemeUpdated;
use App\Models\BaseModel;
use BenSampo\Enum\Traits\CastsEnums;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

/**
 * Class Theme.
 */
class Theme extends BaseModel
{
    use CastsEnums;
    use QueryDsl;
    use Searchable;

    /**
     * @var string[]
     */
    protected $fillable = ['type', 'sequence', 'group'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
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
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['anime.synonyms', 'song']);
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
        $array['song'] = optional($this->song)->toSearchableArray();

        return $array;
    }

    /**
     * The attributes that should be cast to enum types.
     *
     * @var array<string, string>
     */
    protected $enumCasts = [
        'type' => ThemeType::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
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
    public function entries(): HasMany
    {
        return $this->hasMany('App\Models\Wiki\Entry', 'theme_id', 'theme_id');
    }
}
