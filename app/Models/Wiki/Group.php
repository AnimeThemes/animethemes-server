<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Group\GroupCreated;
use App\Events\Wiki\Group\GroupDeleted;
use App\Events\Wiki\Group\GroupDeleting;
use App\Events\Wiki\Group\GroupRestored;
use App\Events\Wiki\Group\GroupUpdated;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeTheme;
use Database\Factories\Wiki\GroupFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Group.
 *
 * @property Collection<int, AnimeTheme> $animethemes
 * @property int $group_id
 * @property string $name
 * @property string $slug
 * 
 * @method static GroupFactory factory(...$parameters)
 */
class Group extends BaseModel
{
    use Actionable;

    final public const TABLE = 'groups';

    final public const ATTRIBUTE_ID = 'group_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SLUG = 'slug';

    final public const RELATION_ANIME = 'animethemes.anime';
    final public const RELATION_THEMES = 'animethemes';
    final public const RELATION_VIDEOS = 'animethemes.animethemeentries.videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        Group::ATTRIBUTE_NAME,
        Group::ATTRIBUTE_SLUG,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => GroupCreated::class,
        'deleted' => GroupDeleted::class,
        'deleting' => GroupDeleting::class,
        'restored' => GroupRestored::class,
        'updated' => GroupUpdated::class,
    ];

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
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subname.
     *
     * @return string
     */
    public function getSubName(): string
    {
        return $this->slug;
    }

    /**
     * Get the themes for the group.
     *
     * @return HasMany
     */
    public function animethemes(): HasMany
    {
        return $this->hasMany(AnimeTheme::class, AnimeTheme::ATTRIBUTE_GROUP);
    }
}
