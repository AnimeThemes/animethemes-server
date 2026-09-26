<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\ThemeStaff\ThemeStaffCreated;
use App\Events\Wiki\ThemeStaff\ThemeStaffDeleted;
use App\Events\Wiki\ThemeStaff\ThemeStaffRestored;
use App\Events\Wiki\ThemeStaff\ThemeStaffUpdated;
use App\Models\BaseModel;
use Database\Factories\Wiki\ThemeStaffFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property int $id
 * @property string|null $alias
 * @property int $artist_id
 * @property Artist $artist
 * @property int $relevance
 * @property string $role
 * @property int $theme_id
 * @property Theme $theme
 *
 * @method static ThemeStaffFactory factory(...$parameters)
 */
#[Table(ThemeStaff::TABLE, ThemeStaff::ATTRIBUTE_ID)]
class ThemeStaff extends BaseModel implements Auditable, SoftDeletable, Sortable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    final public const string TABLE = 'theme_staff';

    final public const string ATTRIBUTE_ID = 'id';

    final public const string ATTRIBUTE_ALIAS = 'alias';

    final public const string ATTRIBUTE_ARTIST = 'artist_id';

    final public const string ATTRIBUTE_RELEVANCE = 'relevance';

    final public const string ATTRIBUTE_ROLE = 'role';

    final public const string ATTRIBUTE_THEME = 'theme_id';

    final public const string RELATION_ARTIST = 'artist';

    final public const string RELATION_THEME = 'theme';

    /**
     * The default roles for theme staff.
     *
     * @var string[]
     */
    public static $roles = [
        '2D Works',
        '2nd Key Animation',
        'Animation Director',
        'Background Art',
        'Background Art Manager',
        'Color Coordination',
        'Finishing',
        'In-Between Check',
        'Key Animation',
        'Photography',
        'Photography Manager',
        'Production Assistant',
        'Special Effects',
        'Storyboard',
        'Unit Director',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ThemeStaffCreated::class,
        'deleted' => ThemeStaffDeleted::class,
        'restored' => ThemeStaffRestored::class,
        'updated' => ThemeStaffUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ThemeStaff::ATTRIBUTE_ALIAS,
        ThemeStaff::ATTRIBUTE_ARTIST,
        ThemeStaff::ATTRIBUTE_ROLE,
        ThemeStaff::ATTRIBUTE_THEME,
    ];

    public $sortable = [
        'order_column_name' => ThemeStaff::ATTRIBUTE_RELEVANCE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ThemeStaff::ATTRIBUTE_ALIAS => 'string',
            ThemeStaff::ATTRIBUTE_ARTIST => 'int',
            ThemeStaff::ATTRIBUTE_ROLE => 'string',
            ThemeStaff::ATTRIBUTE_THEME => 'int',
        ];
    }

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return $this->role;
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->whereBelongsTo($this->theme);
    }

    /**
     * @return BelongsTo<Theme, $this>
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, Theme::ATTRIBUTE_ID);
    }

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, SongStaff::ATTRIBUTE_ARTIST);
    }
}
