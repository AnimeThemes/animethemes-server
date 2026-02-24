<?php

declare(strict_types=1);

namespace App\Pivots\Document;

use App\Enums\Pivots\Document\PageRoleType;
use App\Models\Auth\Role;
use App\Models\Document\Page;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Document\PageRoleFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Page $page
 * @property int $page_id
 * @property Role $role
 * @property int $role_id
 * @property PageRoleType $type
 *
 * @method static PageRoleFactory factory(...$parameters)
 */
class PageRole extends BasePivot
{
    final public const string TABLE = 'page_roles';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_PAGE = 'page_id';
    final public const string ATTRIBUTE_ROLE = 'role_id';
    final public const string ATTRIBUTE_TYPE = 'type';

    final public const string RELATION_PAGE = 'page';
    final public const string RELATION_ROLE = 'role';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = PageRole::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        PageRole::ATTRIBUTE_PAGE,
        PageRole::ATTRIBUTE_ROLE,
        PageRole::ATTRIBUTE_TYPE,
    ];

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            PageRole::ATTRIBUTE_ID,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            PageRole::ATTRIBUTE_TYPE => PageRoleType::class,
        ];
    }

    /**
     * Gets the anime that owns the page role.
     *
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, PageRole::ATTRIBUTE_PAGE);
    }

    /**
     * Gets the role that owns the page role.
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, PageRole::ATTRIBUTE_ROLE);
    }
}
