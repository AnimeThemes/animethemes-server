<?php

namespace App\Models;

use App\Enums\ResourceType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExternalResource extends Model implements Auditable
{

    use CastsEnums;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['type', 'link', 'label'];

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

    protected $enumCasts = [
        'type' => ResourceType::class,
    ];

    protected $casts = [
        'type' => 'int',
    ];

    /**
     * Get the anime that reference this resource
     */
    public function anime() {
        return $this->belongsToMany('App\Models\Anime', 'anime_resource', 'resource_id', 'anime_id');
    }

    /**
     * Get the artists that reference this resource
     */
    public function artists() {
        return $this->belongsToMany('App\Models\Artist', 'artist_resource', 'resource_id', 'artist_id');
    }
}
