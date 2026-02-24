<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Contracts\Models\Nameable;
use App\Models\Document\Page;
use App\Pivots\Document\PageRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as BaseRole;

/**
 * @property string|null $color
 * @property bool $default
 * @property Carbon $created_at
 * @property string $guard_name
 * @property int $id
 * @property string $name
 * @property int $priority
 * @property Carbon $updated_at
 */
class Role extends BaseRole implements Nameable
{
    final public const string TABLE = 'roles';

    final public const string ATTRIBUTE_COLOR = 'color';
    final public const string ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const string ATTRIBUTE_DEFAULT = 'default';
    final public const string ATTRIBUTE_GUARD_NAME = 'guard_name';
    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_PRIORITY = 'priority';
    final public const string ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    final public const string RELATION_PAGES = 'pages';
    final public const string RELATION_PERMISSIONS = 'permissions';
    final public const string RELATION_USERS = 'users';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Role::ATTRIBUTE_DEFAULT => 'boolean',
            Role::ATTRIBUTE_PRIORITY => 'int',
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return BelongsToMany<Page, $this, PageRole>
     */
    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, PageRole::TABLE, PageRole::ATTRIBUTE_ROLE, PageRole::ATTRIBUTE_PAGE)
            ->using(PageRole::class)
            ->withPivot([PageRole::ATTRIBUTE_ID, PageRole::ATTRIBUTE_TYPE])
            ->withTimestamps();
    }
}
