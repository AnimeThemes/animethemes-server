<?php

namespace App\Models;

use App\Contracts\Nameable;
use App\Events\User\UserCreated;
use App\Events\User\UserDeleted;
use App\Events\User\UserRestored;
use App\Events\User\UserUpdated;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, Nameable
{
    use HasApiTokens, HasFactory, HasTeams, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore()
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
        $result = self::withoutEvents(
            function () {
                return $this->save();
            }
        );

        $this->fireModelEvent('restored', false);

        return $result;
    }

    // Make HasTeams more null safe

    /**
     * Determine if the given team is the current team.
     *
     * @param  mixed $team
     * @return bool
     */
    public function isCurrentTeam($team)
    {
        $currentTeam = $this->currentTeam;

        return $currentTeam !== null && $currentTeam->is($team);
    }

    /**
     * Determine if the user owns the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function ownsTeam($team)
    {
        return $team !== null
            && $this->id == $team->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function belongsToTeam($team)
    {
        return $team !== null
            && ($this->teams->contains(function ($t) use ($team) {
                return $t->id === $team->id;
            }) || $this->ownsTeam($team));
    }

    /**
     * Determine if the user has the given permission on the current team.
     *
     * @param  string  $permission
     * @return bool
     */
    public function hasCurrentTeamPermission(string $permission)
    {
        $currentTeam = $this->currentTeam;

        if ($currentTeam === null) {
            return false;
        }

        return $this->hasTeamPermission($currentTeam, $permission);
    }
}
