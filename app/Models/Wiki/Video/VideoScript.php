<?php

declare(strict_types=1);

namespace App\Models\Wiki\Video;

use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Database\Factories\Wiki\Video\VideoScriptFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Nova\Actions\Actionable;

/**
 * Class VideoScript.
 *
 * @property int $script_id
 * @property string $path
 * @property Video $video
 * @property int $video_id
 *
 * @method static VideoScriptFactory factory(...$parameters)
 */
class VideoScript extends BaseModel
{
    use Actionable;

    final public const TABLE = 'video_scripts';

    final public const ATTRIBUTE_ID = 'script_id';
    final public const ATTRIBUTE_PATH = 'path';
    final public const ATTRIBUTE_VIDEO = 'video_id';

    final public const RELATION_VIDEO = 'video';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        VideoScript::ATTRIBUTE_PATH,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => VideoScriptCreated::class,
        'deleted' => VideoScriptDeleted::class,
        'restored' => VideoScriptRestored::class,
        'updated' => VideoScriptUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = VideoScript::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = VideoScript::ATTRIBUTE_ID;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->path;
    }

    /**
     * Get the video that owns the script.
     *
     * @return BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, VideoScript::ATTRIBUTE_VIDEO);
    }
}
