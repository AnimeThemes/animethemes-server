<?php

namespace App\Models;

use App\Enums\ResourceSite;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExternalResource extends Model implements Auditable
{
    use CastsEnums, HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['site', 'link', 'external_id'];

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
     * @var array
     */
    protected $enumCasts = [
        'site' => ResourceSite::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'site' => 'int',
    ];

    /**
     * Get the anime that reference this resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function anime()
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_resource', 'resource_id', 'anime_id')->withPivot('as');
    }

    /**
     * Get the artists that reference this resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_resource', 'resource_id', 'artist_id')->withPivot('as');
    }
}
