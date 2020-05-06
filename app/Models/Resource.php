<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Resource extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resource';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'resource_id';

    /**
     * Get the anime that reference this resource
     */
    public function anime() {
        return $this->belongsToMany('App\Models\Anime');
    }

    /**
     * Get the artists that reference this resource
     */
    public function artists() {
        return $this->belongsToMany('App\Models\Artist');
    }
}
