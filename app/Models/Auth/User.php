<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Concerns\Models\SoftDeletes;
use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Contracts\Models\SoftDeletable;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Models\Admin\ActionLog;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\User\Notification;
use App\Models\User\Report;
use App\Models\Wiki\Video;
use Database\Factories\Auth\UserFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Notifications\DatabaseNotification as FilamentNotification;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User.
 *
 * @property Carbon $created_at
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property Collection<int, ExternalProfile> $externalprofiles
 * @property int $id
 * @property Collection<int, Report> $managedreports
 * @property string $name
 * @property string $password
 * @property Collection<int, Playlist> $playlists
 * @property string $remember_token
 * @property Collection<int, Report> $reports
 * @property Collection<int, PersonalAccessToken> $tokens
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_secret
 * @property Carbon $updated_at
 *
 * @method static UserFactory factory(...$parameters)
 */
class User extends Authenticatable implements FilamentUser, HasAvatar, HasSubtitle, MustVerifyEmail, Nameable, SoftDeletable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    final public const TABLE = 'users';

    final public const ATTRIBUTE_EMAIL = 'email';
    final public const ATTRIBUTE_EMAIL_VERIFIED_AT = 'email_verified_at';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_PASSWORD = 'password';
    final public const ATTRIBUTE_REMEMBER_TOKEN = 'remember_token';
    final public const ATTRIBUTE_TWO_FACTOR_CONFIRMED_AT = 'two_factor_confirmed_at';
    final public const ATTRIBUTE_TWO_FACTOR_RECOVERY_CODES = 'two_factor_recovery_codes';
    final public const ATTRIBUTE_TWO_FACTOR_SECRET = 'two_factor_secret';

    final public const RELATION_EXTERNAL_PROFILES = 'externalprofiles';
    final public const RELATION_MANAGED_REPORTS = 'managedreports';
    final public const RELATION_NOTIFICATIONS = 'notifications';
    final public const RELATION_PERMISSIONS = 'permissions';
    final public const RELATION_PLAYLISTS = 'playlists';
    final public const RELATION_REPORTS = 'reports';
    final public const RELATION_ROLES = 'roles';
    final public const RELATION_ROLES_PERMISSIONS = 'roles.permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        User::ATTRIBUTE_EMAIL,
        User::ATTRIBUTE_NAME,
        User::ATTRIBUTE_PASSWORD,
        User::ATTRIBUTE_EMAIL_VERIFIED_AT,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => UserCreated::class,
        'deleted' => UserDeleted::class,
        'restored' => UserRestored::class,
        'updated' => UserUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = User::TABLE;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        User::ATTRIBUTE_PASSWORD,
        User::ATTRIBUTE_REMEMBER_TOKEN,
        User::ATTRIBUTE_TWO_FACTOR_RECOVERY_CODES,
        User::ATTRIBUTE_TWO_FACTOR_SECRET,
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => 'datetime',
            User::ATTRIBUTE_PASSWORD => 'hashed',
        ];
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        return strval($this->getKey());
    }

    /**
     * Determine if the user can access the filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return ($this->hasVerifiedEmail() && $this->hasAnyPermission(SpecialPermission::VIEW_FILAMENT->value))
            || $this->hasAnyPermission(SpecialPermission::BYPASS_AUTHORIZATION->value);
    }

    /**
     * Get the filament avatar.
     */
    public function getFilamentAvatarUrl(): string
    {
        $hash = md5(Str::lower(Str::trim($this->email)));

        return "https://www.gravatar.com/avatar/$hash";
    }

    /**
     * Get the playlists that belong to the user.
     *
     * @return HasMany<Playlist, $this>
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class, Playlist::ATTRIBUTE_USER);
    }

    /**
     * Get the playlists that belong to the user.
     *
     * @return HasMany<ExternalProfile, $this>
     */
    public function externalprofiles(): HasMany
    {
        return $this->hasMany(ExternalProfile::class, ExternalProfile::ATTRIBUTE_USER);
    }

    /**
     * Get the liked playlists of the user.
     *
     * @return BelongsToMany<Playlist, $this>
     */
    public function likedplaylists(): BelongsToMany
    {
        return $this->belongsToMany(
            Playlist::class,
            Like::TABLE,
            Like::ATTRIBUTE_USER,
            Like::ATTRIBUTE_LIKEABLE_ID,
            null,
            Playlist::ATTRIBUTE_ID
        )
            ->wherePivot(Like::ATTRIBUTE_LIKEABLE_TYPE, Relation::getMorphAlias(Playlist::class))
            ->withTimestamps();
    }

    /**
     * Get the liked videos of the user.
     *
     * @return BelongsToMany<Video, $this>
     */
    public function likedvideos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            Like::TABLE,
            Like::ATTRIBUTE_USER,
            Like::ATTRIBUTE_LIKEABLE_ID,
            null,
            Video::ATTRIBUTE_ID
        )
            ->wherePivot(Like::ATTRIBUTE_LIKEABLE_TYPE, Relation::getMorphAlias(Video::class))
            ->withTimestamps();
    }

    /**
     * Get the submissions that the user made.
     *
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, Report::ATTRIBUTE_USER);
    }

    /**
     * Get the reports that the admin managed.
     *
     * @return HasMany
     */
    public function managedreports(): HasMany
    {
        return $this->hasMany(Report::class, Report::ATTRIBUTE_MODERATOR);
    }

    /**
     * Get the action logs that the user has executed.
     *
     * @return HasMany<ActionLog, $this>
     */
    public function actionlogs(): HasMany
    {
        return $this->hasMany(ActionLog::class, ActionLog::ATTRIBUTE_USER);
    }

    /**
     * Get the entity's notifications.
     *
     * @return MorphMany<Notification, $this>
     */
    public function notifications(): MorphMany
    {
        $notifications = $this->morphMany(Notification::class, 'notifiable')->latest();

        if (Filament::isServing()) {
            return $notifications->where(Notification::ATTRIBUTE_TYPE, FilamentNotification::class);
        }

        return $notifications;
    }

    /**
     * Get the prunable model query.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()->where(
            self::ATTRIBUTE_DELETED_AT,
            ComparisonOperator::LTE->value,
            now()->subMonth()
        );
    }
}
