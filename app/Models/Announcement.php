<?php

namespace App\Models;

use App\Events\Announcement\AnnouncementCreated;
use App\Events\Announcement\AnnouncementDeleted;
use App\Events\Announcement\AnnouncementRestored;
use App\Events\Announcement\AnnouncementUpdated;

class Announcement extends BaseModel
{
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
        'restored' => AnnouncementRestored::class,
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return strval($this->getKey());
    }
}
