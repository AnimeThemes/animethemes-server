<?php

namespace App\Models;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use BenSampo\Enum\Traits\CastsEnums;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Video extends Model implements Auditable, Viewable
{
    use CastsEnums, CustomSearch, HasFactory, InteractsWithViews, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['basename', 'filename', 'path', 'size'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'video_id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['tags'];

    /**
     * @return array
     */
    public function getTagsAttribute()
    {
        $tags = [];

        if ($this->nc) {
            array_push($tags, 'NC');
        }
        if (! empty($this->source) && ($this->source->is(VideoSource::BD) || $this->source->is(VideoSource::DVD))) {
            array_push($tags, $this->source->description);
        }
        if (! empty($this->resolution)) {
            array_push($tags, strval($this->resolution));
        }

        if ($this->subbed) {
            array_push($tags, 'Subbed');
        } elseif ($this->lyrics) {
            array_push($tags, 'Lyrics');
        }

        return $tags;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['entries'] = $this->entries->map(function ($item) {
            return $item->toSearchableArray();
        })->toArray();

        return $array;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'basename';
    }

    /**
     * @var array
     */
    protected $enumCasts = [
        'overlap' => VideoOverlap::class,
        'source' => VideoSource::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'overlap' => 'int',
        'source' => 'int',
        'nc' => 'boolean',
        'subbed' => 'boolean',
        'lyrics' => 'boolean',
        'uncen' => 'boolean',
    ];

    /**
     * Get the related entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entries()
    {
        return $this->belongsToMany('App\Models\Entry', 'entry_video', 'video_id', 'entry_id');
    }
}
