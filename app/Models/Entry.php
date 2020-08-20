<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Entry extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $fillable = ['version', 'episodes', 'nsfw', 'spoiler', 'notes'];

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
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 100;

    /**
     * Gets the theme that owns the entry
     */
    public function theme() {
        return $this->belongsTo('App\Models\Theme', 'theme_id', 'theme_id');
    }

    /**
     * Get the videos linked in the theme entry
     */
    public function videos() {
        return $this->belongsToMany('App\Models\Video', 'entry_video', 'entry_id', 'video_id');
    }

    public function anime() {
        return $this->belongsToThrough('App\Models\Anime', 'App\Models\Theme', null, '', ['App\Models\Anime' => 'anime_id', 'App\Models\Theme' => 'theme_id']);
    }
}
