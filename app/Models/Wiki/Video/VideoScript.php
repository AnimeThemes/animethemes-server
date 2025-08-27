<?php

declare(strict_types=1);

namespace App\Models\Wiki\Video;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptForceDeleting;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Database\Factories\Wiki\Video\VideoScriptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class VideoScript.
 *
 * @property int $script_id
 * @property string $link
 * @property string $path
 * @property Video $video
 * @property int $video_id
 *
 * @method static VideoScriptFactory factory(...$parameters)
 */
class VideoScript extends BaseModel implements InteractsWithSchema, SoftDeletable
{
    use HasFactory;
    use SoftDeletes;

    final public const TABLE = 'video_scripts';

    final public const ATTRIBUTE_ID = 'script_id';
    final public const ATTRIBUTE_LINK = 'link';
    final public const ATTRIBUTE_PATH = 'path';
    final public const ATTRIBUTE_VIDEO = 'video_id';

    final public const RELATION_VIDEO = 'video';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        VideoScript::ATTRIBUTE_PATH,
        VideoScript::ATTRIBUTE_VIDEO,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => VideoScriptCreated::class,
        'deleted' => VideoScriptDeleted::class,
        'forceDeleting' => VideoScriptForceDeleting::class,
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
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        VideoScript::ATTRIBUTE_LINK,
    ];

    /**
     * The link of the video script.
     */
    public function getLinkAttribute(): ?string
    {
        // Necessary for 'make' factories.
        if ($this->hasAttribute(VideoScript::ATTRIBUTE_ID)) {
            return route('videoscript.show', $this);
        }

        return null;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->path;
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        return $this->video->getName();
    }

    /**
     * Get the eager loads needed to the subtitle.
     *
     * @return string[]
     */
    public static function getEagerLoadsForSubtitle(): array
    {
        return [
            VideoScript::RELATION_VIDEO,
        ];
    }

    /**
     * Get the video that owns the script.
     *
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, VideoScript::ATTRIBUTE_VIDEO);
    }

    /**
     * Get the schema for the model.
     */
    public function schema(): ScriptSchema
    {
        return new ScriptSchema();
    }
}
