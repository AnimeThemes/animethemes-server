<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Events\Admin\Announcement\AnnouncementCreated;
use App\Events\Admin\Announcement\AnnouncementDeleted;
use App\Events\Admin\Announcement\AnnouncementUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\AnnouncementFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $announcement_id
 * @property string $content
 * @property bool $public
 *
 * @method static AnnouncementFactory factory(...$parameters)
 * @method static Builder<Announcement> public()
 */
class Announcement extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'announcements';

    final public const string ATTRIBUTE_CONTENT = 'content';
    final public const string ATTRIBUTE_ID = 'announcement_id';
    final public const string ATTRIBUTE_PUBLIC = 'public';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Announcement::ATTRIBUTE_CONTENT,
        Announcement::ATTRIBUTE_PUBLIC,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Announcement::ATTRIBUTE_PUBLIC => 'boolean',
        ];
    }

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
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
    protected $table = Announcement::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Announcement::ATTRIBUTE_ID;

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return $this->getName();
    }

    /**
     * Scope a query to only include public announcements.
     */
    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where(Announcement::ATTRIBUTE_PUBLIC, true);
    }
}
