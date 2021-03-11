<?php

namespace App\Models;

use App\Contracts\Nameable;
use App\Events\Announcement\AnnouncementCreated;
use App\Events\Announcement\AnnouncementDeleted;
use App\Events\Announcement\AnnouncementUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Announcement extends Model implements Auditable, Nameable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['content'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AnnouncementCreated::class,
        'deleted' => AnnouncementDeleted::class,
        'updated' => AnnouncementUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'announcement';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'announcement_id';

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
        return strval($this->getKey());
    }
}
