<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime;

use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use Database\Factories\Wiki\Anime\AnimeSynonymFactory;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

/**
 * Class AnimeSynonym.
 *
 * @property int $synonym_id
 * @property string|null $text
 * @property int $anime_id
 * @property Anime $anime
 * @method static AnimeSynonymFactory factory(...$parameters)
 */
class AnimeSynonym extends BaseModel
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
     * @var array
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
    protected $table = 'anime_synonyms';

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
        return $this->belongsTo('App\Models\Wiki\Anime', 'anime_id', 'anime_id');
    }
}
