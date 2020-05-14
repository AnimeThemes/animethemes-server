<?php

namespace App\Models;

use App\Enums\ThemeType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Theme extends Model implements Auditable
{

    use CastsEnums;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['type', 'sequence', 'group'];

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

    protected $enumCasts = [
        'type' => ThemeType::class,
    ];

    protected $casts = [
        'type' => 'int',
    ];

    public static function boot() {
        parent::boot();

        // registering a callback to be executed upon the creation of an activity AR
        static::creating(function($activity) {
            $slug = $activity->type->key;
            if (!empty($activity->sequence)) {
                $slug .= $activity->sequence;
            }
            $activity->slug = $slug;
        });

        static::updating(function($activity) {
            $slug = $activity->type->key;
            if (!empty($activity->sequence)) {
                $slug .= $activity->sequence;
            }
            $activity->slug = $slug;
        });
    }

    /**
     * Gets the anime that owns the theme
     */
    public function anime() {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the song that the theme uses
     */
    public function song() {
        return $this->belongsTo('App\Models\Song', 'song_id', 'song_id');
    }

    /**
     * Get the entries for the theme
     */
    public function entries() {
        return $this->hasMany('App\Models\Entry', 'theme_id', 'theme_id');
    }
}
