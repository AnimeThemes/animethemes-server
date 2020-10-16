<?php

namespace App\Models;

use App\ScoutElastic\SynonymIndexConfigurator;
use App\ScoutElastic\SynonymSearchRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Synonym extends Model implements Auditable
{
    use HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['text'];

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
     * @var string
     */
    protected $indexConfigurator = SynonymIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        SynonymSearchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'text' => [
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
    ];

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static $allowedSortFields = [
        'synonym_id',
        'created_at',
        'updated_at',
        'text',
        'anime_id',
    ];

    /**
     * Gets the anime that owns the synonym.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anime()
    {
        return $this->belongsTo('App\Models\Anime', 'anime_id', 'anime_id');
    }
}
