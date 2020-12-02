<?php

namespace App\Models;

use App\Enums\ThemeType;
use App\Http\Controllers\Api\ThemeController;
use App\ScoutElastic\ThemeIndexConfigurator;
use App\ScoutElastic\ThemeSearchRule;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Theme extends Model implements Auditable
{
    use CastsEnums, HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['type', 'sequence', 'group'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'theme';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'theme_id';

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['anime'] = optional($this->anime)->toSearchableArray();
        $array['song'] = optional($this->song)->toSearchableArray();

        return $array;
    }

    /**
     * @var string
     */
    protected $indexConfigurator = ThemeIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        ThemeSearchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'slug' => [
                'type' => 'text',
                'copy_to' => ['anime_slug', 'synonym_slug'],
            ],
            'anime' => [
                'type' => 'nested',
                'properties' => [
                    'name' => [
                        'type' => 'text',
                        'copy_to' => ['anime_slug'],
                    ],
                    'synonyms' => [
                        'type' => 'nested',
                        'properties' => [
                            'text' => [
                                'type' => 'text',
                                'copy_to' => ['synonym_slug'],
                            ],
                        ],
                    ],
                ],
            ],
            'anime_slug' => [
                'type' => 'text',
            ],
            'synonym_slug' => [
                'type' => 'text',
            ],
            'song' => [
                'type' => 'nested',
                'properties' => [
                    'title' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $enumCasts = [
        'type' => ThemeType::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'type' => 'int',
    ];

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static $allowedIncludePaths = [
        'anime',
        'anime.images',
        'entries',
        'entries.videos',
        'song',
        'song.artists',
    ];

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static $allowedSortFields = [
        'theme_id',
        'created_at',
        'updated_at',
        'group',
        'type',
        'sequence',
        'slug',
        'anime_id',
        'song_id',
    ];

    /**
     * Gets the anime that owns the theme.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anime()
    {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the song that the theme uses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function song()
    {
        return $this->belongsTo('App\Models\Song', 'song_id', 'song_id');
    }

    /**
     * Get the entries for the theme.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany('App\Models\Entry', 'theme_id', 'theme_id');
    }

    public static function applyFilters($themes, $parser)
    {
        if ($parser->hasFilter(ThemeController::TYPE_QUERY)) {
            $themes = $themes->whereIn(ThemeController::TYPE_QUERY, $parser->getEnumFilter(ThemeController::TYPE_QUERY, ThemeType::class));
        }
        if ($parser->hasFilter(ThemeController::SEQUENCE_QUERY)) {
            $themes = $themes->whereIn(ThemeController::SEQUENCE_QUERY, $parser->getFilter(ThemeController::SEQUENCE_QUERY));
        }
        if ($parser->hasFilter(ThemeController::GROUP_QUERY)) {
            $themes = $themes->whereIn(ThemeController::GROUP_QUERY, $parser->getFilter(ThemeController::GROUP_QUERY));
        }
        
        return $themes;
    }
}
