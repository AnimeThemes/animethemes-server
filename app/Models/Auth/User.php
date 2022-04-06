<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Contracts\Models\Nameable;
use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use Carbon\Carbon;
use Database\Factories\Auth\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasTeams;
use Laravel\Nova\Auth\Impersonatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User.
 *
 * @property Carbon $created_at
 * @property string|null $current_team_id
 * @property Team|null $currentTeam
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property int $id
 * @property string $name
 * @property Collection $ownedTeams
 * @property string $password
 * @property string $remember_token
 * @property Collection $teams
 * @property Collection $tokens
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_secret
 * @property Carbon $updated_at
 *
 * @method static UserFactory factory(...$parameters)
 */
class User extends Authenticatable implements MustVerifyEmail, Nameable
{
    use HasApiTokens;
    use HasFactory;
    use HasTeams;
    use Impersonatable;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    final public const TABLE = 'users';

    final public const ATTRIBUTE_EMAIL = 'email';
    final public const ATTRIBUTE_CURRENT_TEAM = 'current_team_id';
    final public const ATTRIBUTE_DELETED_AT = 'deleted_at';
    final public const ATTRIBUTE_EMAIL_VERIFIED_AT = 'email_verified_at';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_PASSWORD = 'password';
    final public const ATTRIBUTE_REMEMBER_TOKEN = 'remember_token';
    final public const ATTRIBUTE_TWO_FACTOR_RECOVERY_CODES = 'two_factor_recovery_codes';
    final public const ATTRIBUTE_TWO_FACTOR_SECRET = 'two_factor_secret';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        User::ATTRIBUTE_EMAIL,
        User::ATTRIBUTE_NAME,
        User::ATTRIBUTE_PASSWORD,
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
     * @var array<int, string>
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        User::ATTRIBUTE_EMAIL_VERIFIED_AT => 'datetime',
    ];

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

    // Make HasTeams more null safe

    /**
     * Determine if the given team is the current team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function isCurrentTeam($team): bool
    {
        $currentTeam = $this->currentTeam;

        return $currentTeam !== null && $currentTeam->is($team);
    }

    /**
     * Determine if the user has the given permission on the current team.
     *
     * @param  string  $permission
     * @return bool
     */
    public function hasCurrentTeamPermission(string $permission): bool
    {
        return $this->currentTeam !== null && $this->hasTeamPermission($this->currentTeam, $permission);
    }
}
