<?php

namespace App\Models;

use App\Enums\ResourceSite;
use App\Http\Controllers\Api\ExternalResourceController;
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
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static $allowedIncludePaths = [
        'anime',
        'artists',
    ];

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static $allowedSortFields = [
        'resource_id',
        'created_at',
        'updated_at',
        'site',
        'link',
        'external_id',
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

    public static function applyFilters($resources, $parser)
    {
        if ($parser->hasFilter(ExternalResourceController::SITE_QUERY)) {
            $resources = $resources->whereIn(ExternalResourceController::SITE_QUERY, $parser->getEnumFilter(ExternalResourceController::SITE_QUERY, ResourceSite::class));
        }

        return $resources;
    }
}
