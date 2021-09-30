<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\AnimeStudio\AnimeStudioCreated;
use App\Events\Pivot\AnimeStudio\AnimeStudioDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Database\Factories\Pivots\AnimeStudioFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeStudio.
 *
 * @property Anime $anime
 * @property Studio $studio
 * @method static AnimeStudioFactory factory(...$parameters)
 */
class AnimeStudio extends BasePivot
{
    public const TABLE = 'anime_studio';

    public const ATTRIBUTE_ANIME = 'anime_id';
    public const ATTRIBUTE_STUDIO = 'studio_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeStudio::TABLE;

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
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeStudio::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the studio that owns the anime studio.
     *
     * @return BelongsTo
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class, AnimeStudio::ATTRIBUTE_STUDIO);
    }
}
