<?php

namespace App\Models;

use App\Contracts\Nameable;
use App\Events\Synonym\SynonymCreated;
use App\Events\Synonym\SynonymDeleted;
use App\Events\Synonym\SynonymUpdated;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Synonym extends Model implements Auditable, Nameable
{
    use HasFactory, QueryDsl, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
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
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->text;
    }

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
