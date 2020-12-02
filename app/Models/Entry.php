<?php

namespace App\Models;

use App\Http\Controllers\Api\EntryController;
use App\ScoutElastic\EntryIndexConfigurator;
use App\ScoutElastic\EntrySearchRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Entry extends Model implements Auditable
{
    use HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    /**
     * @var array
     */
    protected $fillable = ['version', 'episodes', 'nsfw', 'spoiler', 'notes'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entry';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'entry_id';

    /**
     * @var array
     */
    protected $casts = [
        'nsfw' => 'boolean',
        'spoiler' => 'boolean',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['theme'] = optional($this->theme)->toSearchableArray();

        //Overwrite version with readable format "V{#}"
        if (! empty($this->version)) {
            $array['version'] = Str::of($this->version)->trim()->prepend('V')->__toString();
        }

        return $array;
    }

    /**
     * @var string
     */
    protected $indexConfigurator = EntryIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        EntrySearchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'version' => [
                'type' => 'text',
                'copy_to' => ['version_slug', 'anime_slug', 'synonym_slug'],
            ],
            'theme' => [
                'type' => 'nested',
                'properties' => [
                    'slug' => [
                        'type' => 'text',
                        'copy_to' => ['version_slug', 'anime_slug', 'synonym_slug'],
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
                    'song' => [
                        'type' => 'nested',
                        'properties' => [
                            'title' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
            'version_slug' => [
                'type' => 'text',
            ],
            'anime_slug' => [
                'type' => 'text',
            ],
            'synonym_slug' => [
                'type' => 'text',
            ],
        ],
    ];

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static $allowedIncludePaths = [
        'anime',
        'theme',
        'videos',
    ];

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static $allowedSortFields = [
        'entry_id',
        'created_at',
        'updated_at',
        'version',
        'nsfw',
        'spoiler',
        'theme_id',
    ];

    /**
     * Get the theme that owns the entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme()
    {
        return $this->belongsTo('App\Models\Theme', 'theme_id', 'theme_id');
    }

    /**
     * Get the videos linked in the theme entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function videos()
    {
        return $this->belongsToMany('App\Models\Video', 'entry_video', 'entry_id', 'video_id');
    }

    /**
     * Get the anime that owns the entry through the theme.
     *
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function anime()
    {
        return $this->belongsToThrough('App\Models\Anime', 'App\Models\Theme', null, '', ['App\Models\Anime' => 'anime_id', 'App\Models\Theme' => 'theme_id']);
    }

    public static function applyFilters($entries, $parser)
    {
        if ($parser->hasFilter(EntryController::VERSION_QUERY)) {
            $entries = $entries->whereIn(EntryController::VERSION_QUERY, $parser->getFilter(EntryController::VERSION_QUERY));
        }
        if ($parser->hasFilter(EntryController::NSFW_QUERY)) {
            $entries = $entries->whereIn(EntryController::NSFW_QUERY, $parser->getBooleanFilter(EntryController::NSFW_QUERY));
        }
        if ($parser->hasFilter(EntryController::SPOILER_QUERY)) {
            $entries = $entries->whereIn(EntryController::SPOILER_QUERY, $parser->getBooleanFilter(EntryController::SPOILER_QUERY));
        }

        return $entries;
    }
}
