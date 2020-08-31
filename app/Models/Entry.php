<?php

namespace App\Models;

use App\ScoutElastic\EntryIndexConfigurator;
use App\ScoutElastic\EntrySearchRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Entry extends Model implements Auditable
{

    use Searchable;
    use \OwenIt\Auditing\Auditable;
    use \Znck\Eloquent\Traits\BelongsToThrough;

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

    protected $casts = [
        'nsfw' => 'boolean',
        'spoiler' => 'boolean'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['theme'] = $this->theme->toSearchableArray();

        //Overwrite version with readable format "V{#}"
        if (!empty($this->version)) {
            $array['version'] = Str::of($this->version)->trim()->prepend('V')->__toString();
        }

        return $array;
    }

    protected $indexConfigurator = EntryIndexConfigurator::class;

    protected $searchRules = [
        EntrySearchRule::class
    ];

    protected $mapping = [
        'properties' => [
            'version' => [
                'type' => 'text',
                'copy_to' => ['version_slug', 'anime_slug', 'synonym_slug']
            ],
            'theme' => [
                'type' => 'nested',
                'properties' => [
                    'slug' => [
                        'type' => 'text',
                        'copy_to' => ['version_slug', 'anime_slug', 'synonym_slug']
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
     * Gets the theme that owns the entry
     */
    public function theme() {
        return $this->belongsTo('App\Models\Theme', 'theme_id', 'theme_id');
    }

    /**
     * Get the videos linked in the theme entry
     */
    public function videos() {
        return $this->belongsToMany('App\Models\Video', 'entry_video', 'entry_id', 'video_id');
    }

    public function anime() {
        return $this->belongsToThrough('App\Models\Anime', 'App\Models\Theme', null, '', ['App\Models\Anime' => 'anime_id', 'App\Models\Theme' => 'theme_id']);
    }
}
