<?php

namespace App\Models;

use App\Enums\ThemeType;
use App\ScoutElastic\ThemeIndexConfigurator;
use App\ScoutElastic\ThemeSearchRule;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Theme extends Model implements Auditable
{

    use CastsEnums, Searchable;
    use \OwenIt\Auditing\Auditable;

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
        $array['anime'] = $this->anime->toSearchableArray();
        return $array;
    }

    protected $indexConfigurator = ThemeIndexConfigurator::class;

    protected $searchRules = [
        ThemeSearchRule::class
    ];

    protected $mapping = [
        'properties' => [
            'slug' => [
                'type' => 'text',
                'copy_to' => ['anime_slug', 'synonym_slug']
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
            'anime_slug' => [
                'type' => 'text'
            ],
            'synonym_slug' => [
                'type' => 'text'
            ]
        ]
    ];

    protected $enumCasts = [
        'type' => ThemeType::class,
    ];

    protected $casts = [
        'type' => 'int',
    ];

    public static function boot() {
        parent::boot();

        // By default, the Theme Slug is the Type "{OP|ED}"
        // If a sequence number is specified, the Theme Slug is "{OP|ED}{Sequence}"
        static::creating(function($activity) {
            $slug = $activity->type->key;
            if (!empty($activity->sequence)) {
                $slug .= $activity->sequence;
            }
            $activity->slug = $slug;
        });

        static::updating(function($activity) {
            $slug = $activity->type->key;
            if (!empty($activity->sequence)) {
                $slug .= $activity->sequence;
            }
            $activity->slug = $slug;
        });
    }

    /**
     * Gets the anime that owns the theme
     */
    public function anime() {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the song that the theme uses
     */
    public function song() {
        return $this->belongsTo('App\Models\Song', 'song_id', 'song_id');
    }

    /**
     * Get the entries for the theme
     */
    public function entries() {
        return $this->hasMany('App\Models\Entry', 'theme_id', 'theme_id');
    }
}
