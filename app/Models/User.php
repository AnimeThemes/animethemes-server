<?php

namespace App\Models;

use App\Enums\UserType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use CastsEnums;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
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
     * @var array
     */
    protected $enumCasts = [
        'type' => UserType::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'type' => 'int',
    ];

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->type->is(UserType::ADMIN);
    }

    /**
     * @return boolean
     */
    public function isContributor()
    {
        return $this->type->is(UserType::CONTRIBUTOR);
    }

    /**
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->type->is(UserType::READ_ONLY);
    }
}
