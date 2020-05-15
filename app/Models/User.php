<?php

namespace App\Models;

use App\Enums\UserType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use CastsEnums;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

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

    public function isAdmin() {
        return $this->type->is(UserType::ADMIN);
    }

    public function isContributor() {
        return $this->type->is(UserType::CONTRIBUTOR);
    }

    public function isReadOnly() {
        return $this->type->is(UserType::READ_ONLY);
    }
}
