<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Theme extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

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
     * Gets the anime that owns the theme
     */
    public function anime() {
        return $this->belongsTo('App\Models\Anime');
    }

    /**
     * Get the entries for the theme
     */
    public function entries() {
        return $this->hasMany('App\Models\Entry');
    }

    /**
     * Gets the song that the theme uses
     */
    public function song() {
        return $this->belongsTo('App\Models\Song');
    }
}
