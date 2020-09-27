<?php

namespace App\Models;

use App\Enums\OverlapType;
use App\Enums\SourceType;
use App\ScoutElastic\VideoIndexConfigurator;
use App\ScoutElastic\VideoSearchRule;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Video extends Model implements Auditable
{

    use CastsEnums, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['basename', 'filename', 'path'];

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

    public function getTagsAttribute() : array {
        $tags = [];

        if ($this->nc) {
            array_push($tags, 'NC');
        }
        if (!empty($this->source) && ($this->source->is(SourceType::BD) || $this->source->is(SourceType::DVD))) {
            array_push($tags, $this->source->description);
        }
        if (!empty($this->resolution)) {
            array_push($tags, strval($this->resolution));
        }

        if ($this->subbed) {
            array_push($tags, 'Subbed');
        } else if ($this->lyrics) {
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
        $array['entries'] = $this->entries->map(function($item) {
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
        VideoSearchRule::class
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'filename' => [
                'type' => 'text'
            ],
            'tags' => [
                'type' => 'text',
                'copy_to' => ['tags_slug', 'anime_slug', 'synonym_slug']
            ],
            'entries' => [
                'type' => 'nested',
                'properties' => [
                    'version' => [
                        'type' => 'text',
                        'copy_to' => ['tags_slug', 'version_slug', 'anime_slug', 'synonym_slug']
                    ],
                    'theme' => [
                        'type' => 'nested',
                        'properties' => [
                            'slug' => [
                                'type' => 'text',
                                'copy_to' => ['tags_slug', 'version_slug', 'anime_slug', 'synonym_slug']
                            ],
                            'anime' => [
                                'type' => 'nested',
                                'properties' => [
                                    'name' => [
                                        'type' => 'text',
                                        'copy_to' => ['anime_slug']
                                    ],
                                    'synonyms' => [
                                        'type' => 'nested',
                                        'properties' => [
                                            'text' => [
                                                'type' => 'text',
                                                'copy_to' => ['synonym_slug']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'song' => [
                                'type' => 'nested',
                                'properties' => [
                                    'title' => [
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            ],
            'tags_slug' => [
                'type' => 'text'
            ],
            'version_slug' => [
                'type' => 'text'
            ],
            'anime_slug' => [
                'type' => 'text'
            ],
            'synonym_slug' => [
                'type' => 'text'
            ]
        ]
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
        'overlap' => OverlapType::class,
        'source' => SourceType::class,
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
     * Get the related entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entries() : BelongsToMany {
        return $this->belongsToMany('App\Models\Entry', 'entry_video', 'video_id', 'entry_id');
    }
}
