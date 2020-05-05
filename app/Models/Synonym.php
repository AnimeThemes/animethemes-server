<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Synonym extends Model
{
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
        return $this->belongsTo('App\Models\Anime');
    }
}
