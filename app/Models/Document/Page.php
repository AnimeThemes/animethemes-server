<?php

declare(strict_types=1);

namespace App\Models\Document;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Events\Document\Page\PageCreated;
use App\Events\Document\Page\PageDeleted;
use App\Events\Document\Page\PageRestored;
use App\Events\Document\Page\PageUpdated;
use App\Models\BaseModel;
use Database\Factories\Document\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $body
 * @property string $name
 * @property int $page_id
 * @property string $slug
 *
 * @method static PageFactory factory(...$parameters)
 */
class Page extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'pages';

    final public const string ATTRIBUTE_BODY = 'body';
    final public const string ATTRIBUTE_ID = 'page_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SLUG = 'slug';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Page::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Page::ATTRIBUTE_ID;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => PageCreated::class,
        'deleted' => PageDeleted::class,
        'restored' => PageRestored::class,
        'updated' => PageUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Page::ATTRIBUTE_BODY,
        Page::ATTRIBUTE_NAME,
        Page::ATTRIBUTE_SLUG,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        Page::ATTRIBUTE_BODY,
    ];

    /**
     * Get the route key for the model.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Page::ATTRIBUTE_SLUG;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->slug;
    }
}
