<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Synonym extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['text'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'synonym';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'synonym_id';

    /**
     * Gets the anime that owns the synonym
     */
    public function anime() {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }
}
