<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Contracts\Models\Nameable;
use App\Contracts\Models\HasSubtitle;
use App\Enums\Auth\SpecialPermission;
use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Models\Admin\ActionLog;
use App\Models\Admin\Report;
use App\Models\List\Playlist;
use App\Models\List\ExternalProfile;
use App\Models\User\Notification;
use App\Notifications\UserNotification;
use Database\Factories\Auth\UserFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Notifications\DatabaseNotification as FilamentNotification;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property Carbon $deleted_at
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
class User extends Authenticatable implements MustVerifyEmail, Nameable, HasSubtitle, FilamentUser, HasAvatar
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable {
        notifications as traitNotifications;
    }
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    final public const TABLE = 'users';

    final public const ATTRIBUTE_EMAIL = 'email';
    final public const ATTRIBUTE_DELETED_AT = 'deleted_at';
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
     * @var array
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
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->email;
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore(): ?bool
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = null;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        // Save quietly so that we do not fire an updated event on restore
        $result = $this->saveQuietly();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Determine if the user can access the filament panel.
     *
     * @param  Panel  $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail() && $this->hasAnyPermission(SpecialPermission::VIEW_FILAMENT->value);
    }

    /**
     * Get the filament avatar.
     *
     * @return string
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
        if (Filament::isServing()) {
            return $this->traitNotifications()->where(Notification::ATTRIBUTE_TYPE, FilamentNotification::class);
        }

        return $this->traitNotifications()->where(Notification::ATTRIBUTE_TYPE, UserNotification::class);
    }
}
