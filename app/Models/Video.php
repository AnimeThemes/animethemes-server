<?php

namespace App\Models;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Enums\AnimeSeason;
use App\Enums\ThemeType;
use App\Http\Controllers\Api\AnimeController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\EntryCollection;
use App\ScoutElastic\VideoIndexConfigurator;
use App\ScoutElastic\VideoSearchRule;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Video extends Model implements Auditable
{
    use CastsEnums, HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['basename', 'filename', 'path', 'size'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'video_id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['tags'];

    /**
     * @return array
     */
    public function getTagsAttribute()
    {
        $tags = [];

        if ($this->nc) {
            array_push($tags, 'NC');
        }
        if (! empty($this->source) && ($this->source->is(VideoSource::BD) || $this->source->is(VideoSource::DVD))) {
            array_push($tags, $this->source->description);
        }
        if (! empty($this->resolution)) {
            array_push($tags, strval($this->resolution));
        }

        if ($this->subbed) {
            array_push($tags, 'Subbed');
        } elseif ($this->lyrics) {
            array_push($tags, 'Lyrics');
        }

        return $tags;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['entries'] = $this->entries->map(function ($item) {
            return $item->toSearchableArray();
        })->toArray();

        return $array;
    }

    /**
     * @var string
     */
    protected $indexConfigurator = VideoIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        VideoSearchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'filename' => [
                'type' => 'text',
            ],
            'tags' => [
                'type' => 'text',
                'copy_to' => ['tags_slug', 'anime_slug', 'synonym_slug'],
            ],
            'entries' => [
                'type' => 'nested',
                'properties' => [
                    'version' => [
                        'type' => 'text',
                        'copy_to' => ['tags_slug', 'version_slug', 'anime_slug', 'synonym_slug'],
                    ],
                    'theme' => [
                        'type' => 'nested',
                        'properties' => [
                            'slug' => [
                                'type' => 'text',
                                'copy_to' => ['tags_slug', 'version_slug', 'anime_slug', 'synonym_slug'],
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
                ],
            ],
            'tags_slug' => [
                'type' => 'text',
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'basename';
    }

    /**
     * @var array
     */
    protected $enumCasts = [
        'overlap' => VideoOverlap::class,
        'source' => VideoSource::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'overlap' => 'int',
        'source' => 'int',
        'nc' => 'boolean',
        'subbed' => 'boolean',
        'lyrics' => 'boolean',
        'uncen' => 'boolean',
    ];

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static $allowedIncludePaths = [
        'entries',
        'entries.theme',
        'entries.theme.anime',
    ];

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static $allowedSortFields = [
        'video_id',
        'created_at',
        'updated_at',
        'filename',
        'path',
        'size',
        'basename',
        'resolution',
        'nc',
        'subbed',
        'lyrics',
        'uncen',
        'source',
        'overlap',
    ];

    /**
     * Get the related entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entries()
    {
        return $this->belongsToMany('App\Models\Entry', 'entry_video', 'video_id', 'entry_id');
    }

    /**
     * Apply filters to Video resource from parser
     * 
     * @param \Illuminate\Database\Eloquent\Builder $anime
     * @param \App\JsonApi\QueryParser $parser
     * @return mixed
     */
    public static function applyFilters($videos, $parser)
    {
        // apply filters
        if ($parser->hasFilter(VideoController::RESOLUTION_QUERY)) {
            $videos = $videos->whereIn(VideoController::RESOLUTION_QUERY, $parser->getFilter(VideoController::RESOLUTION_QUERY));
        }
        if ($parser->hasFilter(VideoController::NC_QUERY)) {
            $videos = $videos->whereIn(VideoController::NC_QUERY, $parser->getBooleanFilter(VideoController::NC_QUERY));
        }
        if ($parser->hasFilter(VideoController::SUBBED_QUERY)) {
            $videos = $videos->whereIn(VideoController::SUBBED_QUERY, $parser->getBooleanFilter(VideoController::SUBBED_QUERY));
        }
        if ($parser->hasFilter(VideoController::LYRICS_QUERY)) {
            $videos = $videos->whereIn(VideoController::LYRICS_QUERY, $parser->getBooleanFilter(VideoController::LYRICS_QUERY));
        }
        if ($parser->hasFilter(VideoController::UNCEN_QUERY)) {
            $videos = $videos->whereIn(VideoController::UNCEN_QUERY, $parser->getBooleanFilter(VideoController::UNCEN_QUERY));
        }
        if ($parser->hasFilter(VideoController::SOURCE_QUERY)) {
            $videos = $videos->whereIn(VideoController::SOURCE_QUERY, $parser->getEnumFilter(VideoController::SOURCE_QUERY, VideoSource::class));
        }
        if ($parser->hasFilter(VideoController::OVERLAP_QUERY)) {
            $videos = $videos->whereIn(VideoController::OVERLAP_QUERY, $parser->getEnumFilter(VideoController::OVERLAP_QUERY, VideoOverlap::class));
        }
        
        // apply relational entry filters
        if ($parser->hasFilter(EntryController::VERSION_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereIn(EntryController::VERSION_QUERY, $parser->getFilter(EntryController::VERSION_QUERY));
            });
        }
        if ($parser->hasFilter(EntryController::NSFW_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereIn(EntryController::NSFW_QUERY, $parser->getBooleanFilter(EntryController::NSFW_QUERY));
            });
        }
        if ($parser->hasFilter(EntryController::SPOILER_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereIn(EntryController::SPOILER_QUERY, $parser->getBooleanFilter(EntryController::SPOILER_QUERY));
            });
        }
        
        if ($parser->hasFilter(EntryController::NSFW_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereIn(EntryController::NSFW_QUERY, $parser->getBooleanFilter(EntryController::NSFW_QUERY));
            });
        }
        
        // apply relational theme filters
        if ($parser->hasFilter(ThemeController::TYPE_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereHas('theme', function($query) use($parser) {
                    $query->whereIn(ThemeController::TYPE_QUERY, $parser->getEnumFilter(ThemeController::TYPE_QUERY, ThemeType::class));
                });
            });
        }
        if ($parser->hasFilter(ThemeController::SEQUENCE_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereHas('theme', function($query) use($parser) {
                    $query->whereIn(ThemeController::SEQUENCE_QUERY, $parser->getFilter(ThemeController::SEQUENCE_QUERY));
                });
            });
        }
        if ($parser->hasFilter(ThemeController::GROUP_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereHas('theme', function($query) use($parser) {
                    $query->whereIn(ThemeController::GROUP_QUERY, $parser->getFilter(ThemeController::GROUP_QUERY));
                });
            });
        }
        
        // apply relational anime filters
        if ($parser->hasFilter(AnimeController::YEAR_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereHas('theme', function($query) use($parser) {
                    $query->whereHas(AnimeCollection::$wrap, function($query) use($parser) {
                        $query->whereIn(AnimeController::YEAR_QUERY, $parser->getFilter(AnimeController::YEAR_QUERY));
                    });
                });
            });
        }
        if ($parser->hasFilter(AnimeController::SEASON_QUERY)) {
            $videos = $videos->whereHas(EntryCollection::$wrap, function($query) use($parser) {
                $query->whereHas('theme', function($query) use($parser) {
                    $query->whereHas(AnimeCollection::$wrap, function($query) use($parser) {
                        $query->whereIn(AnimeController::SEASON_QUERY, $parser->getEnumFilter(AnimeController::SEASON_QUERY, AnimeSeason::class));
                    });
                });
            });
        }

        return $videos;
    }
}
