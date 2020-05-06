<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Entry extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

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
     * Gets the theme that owns the entry
     */
    public function theme() {
        return $this->belongsTo('App\Models\Theme');
    }

    /**
     * Get the videos linked in the theme entry
     */
    public function videos() {
        return $this->belongsToMany('App\Models\Video');
    }
}
