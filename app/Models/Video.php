<?php

namespace App\Models;

use App\Enums\OverlapType;
use App\Enums\SourceType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Video extends Model implements Auditable
{

    use CastsEnums;
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'basename';
    }

    protected $enumCasts = [
        'overlap' => OverlapType::class,
        'source' => SourceType::class,
    ];

    protected $casts = [
        'overlap' => 'int',
        'source' => 'int',
    ];

    /**
     * Get the referencing entries
     */
    public function entries() {
        return $this->belongsToMany('App\Models\Entry', 'entry_video', 'video_id', 'entry_id');
    }
}
