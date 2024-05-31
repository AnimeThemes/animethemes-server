<?php

declare(strict_types=1);

namespace App\Models\Discord;

use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DiscordThread.
 *
 * @property Anime $anime
 * @property int $thread_id
 * @property string $name
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
        return $this->getKey();
    }

    /**
     * Gets the anime that the thread uses.
     *
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, DiscordThread::ATTRIBUTE_ANIME);
    }
}