<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioCreated;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\AnimeStudioFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeStudio.
 *
 * @property Anime $anime
 * @property int $anime_id
 * @property Studio $studio
 * @property int $studio_id
 *
 * @method static AnimeStudioFactory factory(...$parameters)
 */
class AnimeStudio extends BasePivot
{
    final public const TABLE = 'anime_studio';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_STUDIO = 'studio_id';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_STUDIO = 'studio';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeStudio::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            AnimeStudio::ATTRIBUTE_ANIME,
            AnimeStudio::ATTRIBUTE_STUDIO,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        AnimeStudio::ATTRIBUTE_ANIME,
        AnimeStudio::ATTRIBUTE_STUDIO,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AnimeStudioCreated::class,
        'deleted' => AnimeStudioDeleted::class,
    ];

    /**
     * Gets the anime that owns the anime studio.
     *
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeStudio::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the studio that owns the anime studio.
     *
     * @return BelongsTo<Studio, $this>
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class, AnimeStudio::ATTRIBUTE_STUDIO);
    }
}
