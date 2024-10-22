<?php

declare(strict_types=1);

namespace App\Models\Discord;

use App\Events\Discord\DiscordThread\DiscordThreadDeleted;
use App\Events\Discord\DiscordThread\DiscordThreadUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use Database\Factories\Discord\DiscordThreadFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DiscordThread.
 *
 * @property Anime $anime
 * @property int $anime_id
 * @property string $thread_id
 * @property string $name
 *
 * @method static DiscordThreadFactory factory(...$parameters)
 */
class DiscordThread extends BaseModel
{
    final public const TABLE = 'discord_threads';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_ID = 'thread_id';
    final public const ATTRIBUTE_NAME = 'name';

    final public const RELATION_ANIME = 'anime';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        DiscordThread::ATTRIBUTE_ANIME,
        DiscordThread::ATTRIBUTE_ID,
        DiscordThread::ATTRIBUTE_NAME,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = DiscordThread::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = DiscordThread::ATTRIBUTE_ID;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleted' => DiscordThreadDeleted::class,
        'updated' => DiscordThreadUpdated::class,
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->anime->getName();
    }

    /**
     * Gets the anime that the thread uses.
     *
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, DiscordThread::ATTRIBUTE_ANIME);
    }
}