<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Video extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['basename', 'filename', 'path'];

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
     * Get the referencing entries
     */
    public function entries() {
        return $this->belongsToMany('App\Models\Entry');
    }
}
