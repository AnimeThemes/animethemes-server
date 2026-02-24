<?php

declare(strict_types=1);

namespace App\Models\Document;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Pivots\Document\PageRoleType;
use App\Events\Document\Page\PageCreated;
use App\Events\Document\Page\PageDeleted;
use App\Events\Document\Page\PageRestored;
use App\Events\Document\Page\PageUpdated;
use App\Models\Auth\Role;
use App\Models\BaseModel;
use App\Pivots\Document\PageRole;
use App\Scopes\ReadablePagesScope;
use Database\Factories\Document\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $body
 * @property Collection<int, Role> $editorRoles
 * @property string $name
 * @property int|null $next_id
 * @property Page|null $next
 * @property int $page_id
 * @property int|null $previous_id
 * @property Page|null $previous
 * @property Collection<int, Role> $roles
 * @property string $slug
 * @property Collection<int, Role> $viewerRoles
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
    final public const string ATTRIBUTE_NEXT = 'next_id';
    final public const string ATTRIBUTE_PREVIOUS = 'previous_id';
    final public const string ATTRIBUTE_SLUG = 'slug';

    final public const string RELATION_EDITOR_ROLES = 'editorRoles';
    final public const string RELATION_NEXT = 'next';
    final public const string RELATION_PREVIOUS = 'previous';
    final public const string RELATION_ROLES = 'roles';
    final public const string RELATION_VIEWER_ROLES = 'viewerRoles';

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
        Page::ATTRIBUTE_NEXT,
        Page::ATTRIBUTE_PREVIOUS,
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
     * The "boot" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new ReadablePagesScope);
    }

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

    public function isPublic(): bool
    {
        return $this->viewerRoles()->doesntExist();
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function next(): BelongsTo
    {
        return $this->belongsTo(Page::class, Page::ATTRIBUTE_NEXT);
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function previous(): BelongsTo
    {
        return $this->belongsTo(Page::class, Page::ATTRIBUTE_PREVIOUS);
    }

    /**
     * @return BelongsToMany<Role, $this, PageRole>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, PageRole::TABLE, PageRole::ATTRIBUTE_PAGE, PageRole::ATTRIBUTE_ROLE)
            ->using(PageRole::class)
            ->withPivot([PageRole::ATTRIBUTE_ID, PageRole::ATTRIBUTE_TYPE])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Role, $this, PageRole>
     */
    public function viewerRoles(): BelongsToMany
    {
        return $this->roles()->wherePivot(PageRole::ATTRIBUTE_TYPE, PageRoleType::VIEWER->value);
    }

    /**
     * @return BelongsToMany<Role, $this, PageRole>
     */
    public function editorRoles(): BelongsToMany
    {
        return $this->roles()->wherePivot(PageRole::ATTRIBUTE_TYPE, PageRoleType::EDITOR->value);
    }
}
