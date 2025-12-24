<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Group\GroupCreated;
use App\Events\Wiki\Group\GroupDeleted;
use App\Events\Wiki\Group\GroupDeleting;
use App\Events\Wiki\Group\GroupRestored;
use App\Events\Wiki\Group\GroupUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeTheme;
use Database\Factories\Wiki\GroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property Collection<int, AnimeTheme> $animethemes
 * @property int $group_id
 * @property string $name
 * @property string $slug
 *
 * @method static GroupFactory factory(...$parameters)
 */
class Group extends BaseModel implements Auditable, SoftDeletable
{
    use HasAudits;
    use HasFactory;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'groups';

    final public const string ATTRIBUTE_ID = 'group_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SLUG = 'slug';

    final public const string RELATION_ANIME = 'animethemes.anime';
    final public const string RELATION_THEMES = 'animethemes';
    final public const string RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Group::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Group::ATTRIBUTE_ID;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => GroupCreated::class,
        'deleted' => GroupDeleted::class,
        'deleting' => GroupDeleting::class,
        'restored' => GroupRestored::class,
        'updated' => GroupUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Group::ATTRIBUTE_NAME,
        Group::ATTRIBUTE_SLUG,
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * @return HasMany<AnimeTheme, $this>
     */
    public function animethemes(): HasMany
    {
        return $this->hasMany(AnimeTheme::class, AnimeTheme::ATTRIBUTE_GROUP);
    }
}
