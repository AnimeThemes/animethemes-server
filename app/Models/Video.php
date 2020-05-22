<?php

namespace App\Models;

use App\Enums\OverlapType;
use App\Enums\SourceType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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

    public static function boot() {
        parent::boot();

        // Try to infer additional attributes from filename
        static::creating(function($activity) {
            try {
                // Match Tags of filename
                // Format: "{Base Name}-{OP|ED}{Sequence}v{Version}-{Tags}"
                preg_match('/\A.*\-[O|E][P|D].*\-(.*)/', $activity->filename, $tags_match);

                // Check if the filename has tags, which is not guaranteed
                if (!empty($tags_match)) {
                    $tags = $tags_match[1];

                    // Set true/false if tag is included/excluded
                    $activity->nc = Str::contains($tags, 'NC');
                    $activity->subbed = Str::contains($tags, 'Subbed');
                    $activity->lyrics = Str::contains($tags, 'Lyrics');
                    // Note: Our naming convention does not include "Uncen"

                    // Set resolution to numeric tag if included
                    preg_match('/\d+/', $tags, $resolution);
                    if (!empty($resolution)) {
                        $activity->resolution = intval($resolution[0]);
                    }

                    // Special cases for implicit resolution
                    if (in_array($tags, ['NCBD', 'NCBDLyrics'])) {
                        $activity->resolution = 720;
                    }

                    // Set source type for first matching tag to key
                    foreach (SourceType::getKeys() as $source_key) {
                        if (Str::contains($tags, $source_key)) {
                            $activity->source = SourceType::getValue($source_key);
                            break;
                        }
                    }

                    // Note: Our naming convention does not include Overlap type
                }
            } catch (Exception $exception) {
                Log::error($exception);
            }
        });
    }

    /**
     * Get the referencing entries
     */
    public function entries() {
        return $this->belongsToMany('App\Models\Entry', 'entry_video', 'video_id', 'entry_id');
    }
}
