<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\Synonym\SynonymCreated;
use App\Events\Synonym\SynonymDeleted;
use App\Events\Synonym\SynonymRestored;
use App\Events\Synonym\SynonymUpdated;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

/**
 * Class Synonym.
 */
class Synonym extends BaseModel
{
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['text'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => SynonymCreated::class,
        'deleted' => SynonymDeleted::class,
        'restored' => SynonymRestored::class,
        'updated' => SynonymUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'synonym';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'synonym_id';

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->text;
    }

    /**
     * Gets the anime that owns the synonym.
     *
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }
}
