<?php

declare(strict_types=1);

namespace App\Models\List;

use App\Enums\Models\List\ExternalResourceListType;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;

/**
 * Class UserExternalProfile.
 *
 * @property int $profile_id
 * @property string $username
 * @property ExternalResourceListType $site
 * @property int|null $user_id
 * @property Collection<int, UserExternalListEntry> $entries
 * @property User|null $user
 */
class UserExternalProfile extends BaseModel
{
    use Actionable;

    final public const TABLE = 'user_external_profiles';

    final public const ATTRIBUTE_ID = 'profile_id';
    final public const ATTRIBUTE_USERNAME = 'username';
    final public const ATTRIBUTE_SITE = 'site';
    final public const ATTRIBUTE_USER = 'user_id';

    final public const RELATION_ENTRIES = 'entries';
    final public const RELATION_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        UserExternalProfile::ATTRIBUTE_USERNAME,
        UserExternalProfile::ATTRIBUTE_SITE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = UserExternalProfile::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = UserExternalProfile::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        UserExternalProfile::ATTRIBUTE_SITE => ExternalResourceListType::class,
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->username;
    }

    /**
     * Get the entries for the user profile.
     *
     * @return HasMany
     */
    public function entries(): HasMany
    {
        return $this->hasMany(UserExternalListEntry::class, UserExternalListEntry::ATTRIBUTE_USER_PROFILE);
    }

    /**
     * Get the user that owns the user profile.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserExternalProfile::ATTRIBUTE_USER);
    }
}
