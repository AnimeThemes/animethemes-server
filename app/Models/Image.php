<?php

namespace App\Models;

use App\Enums\ImageFacet;
use App\Http\Controllers\Api\ImageController;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Image extends Model implements Auditable
{
    use CastsEnums, HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['path', 'facet'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'image';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'image_id';

    /**
     * @var array
     */
    protected $enumCasts = [
        'facet' => ImageFacet::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'facet' => 'int',
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
        'image_id',
        'created_at',
        'updated_at',
        'path',
        'facet',
    ];

    /**
     * Get the anime that use this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function anime()
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_image', 'image_id', 'anime_id');
    }

    /**
     * Get the artists that use this image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_image', 'image_id', 'artist_id');
    }

    public static function applyFilters($images, $parser)
    {
        if ($parser->hasFilter(ImageController::FACET_QUERY)) {
            $images = $images->whereIn(ImageController::FACET_QUERY, $parser->getEnumFilter(ImageController::FACET_QUERY, ImageFacet::class));
        }
        return $images;
    }
}
