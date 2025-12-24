<?php

declare(strict_types=1);

namespace App\Models\Discord;

use App\Events\Discord\DiscordThread\DiscordThreadDeleted;
use App\Events\Discord\DiscordThread\DiscordThreadUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Observers\Discord\DiscordThreadObserver;
use Database\Factories\Discord\DiscordThreadFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Anime $anime
 * @property int $anime_id
 * @property string $thread_id
 * @property string $name
 *
 * @method static DiscordThreadFactory factory(...$parameters)
 */
#[ObservedBy(DiscordThreadObserver::class)]
class DiscordThread extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'discord_threads';

    final public const string ATTRIBUTE_ANIME = 'anime_id';
    final public const string ATTRIBUTE_ID = 'thread_id';
    final public const string ATTRIBUTE_NAME = 'name';

    final public const string RELATION_ANIME = 'anime';

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
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'deleted' => DiscordThreadDeleted::class,
        'updated' => DiscordThreadUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        DiscordThread::ATTRIBUTE_ANIME,
        DiscordThread::ATTRIBUTE_ID,
        DiscordThread::ATTRIBUTE_NAME,
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->anime->getName();
    }

    /**
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, DiscordThread::ATTRIBUTE_ANIME);
    }
}
