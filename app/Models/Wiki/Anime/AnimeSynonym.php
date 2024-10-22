<?php

declare(strict_types=1);

namespace App\Models\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use Database\Factories\Wiki\Anime\AnimeSynonymFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeSynonym.
 *
 * @property Anime $anime
 * @property int $anime_id
 * @property int $synonym_id
 * @property string|null $text
 * @property AnimeSynonymType $type
 *
 * @method static AnimeSynonymFactory factory(...$parameters)
 */
class AnimeSynonym extends BaseModel
{
    use Searchable;

    final public const TABLE = 'anime_synonyms';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_ID = 'synonym_id';
    final public const ATTRIBUTE_TEXT = 'text';
    final public const ATTRIBUTE_TYPE = 'type';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_VIDEOS = 'anime.animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        AnimeSynonym::ATTRIBUTE_ANIME,
        AnimeSynonym::ATTRIBUTE_TEXT,
        AnimeSynonym::ATTRIBUTE_TYPE,
    ];

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
    protected $table = AnimeSynonym::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = AnimeSynonym::ATTRIBUTE_ID;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            AnimeSynonym::ATTRIBUTE_TYPE => AnimeSynonymType::class,
        ];
    }

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
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->anime->getName();
    }

    /**
     * Gets the anime that owns the synonym.
     *
     * @return BelongsTo<Anime, $this>
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class, AnimeSynonym::ATTRIBUTE_ANIME);
    }
}
