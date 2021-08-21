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
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_studio';

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
        return $this->belongsTo('App\Models\Wiki\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the studio that owns the anime studio.
     *
     * @return BelongsTo
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Studio', 'studio_id', 'studio_id');
    }
}
