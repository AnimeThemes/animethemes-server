<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementRestored;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\AnnouncementFactory;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Announcement.
 *
 * @property int $announcement_id
 * @property string $content
 *
 * @method static AnnouncementFactory factory(...$parameters)
 */
class Announcement extends BaseModel
{
    use Actionable;

    final public const TABLE = 'announcements';

    final public const ATTRIBUTE_CONTENT = 'content';
    final public const ATTRIBUTE_ID = 'announcement_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        Announcement::ATTRIBUTE_CONTENT,
    ];

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
    protected $table = Announcement::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Announcement::ATTRIBUTE_ID;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }
}
