<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Enums\UserType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{

    use CastsEnums;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitation';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'invitation_id';

    protected $enumCasts = [
        'type' => UserType::class,
        'status' => InvitationStatus::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'int',
        'status' => 'int',
    ];

    public static function boot() {
        parent::boot();

        static::creating(function($activity) {
            $activity->token = Str::random(rand(20, 100));
        });
    }

    public function isOpen() {
        return $this->status->is(InvitationStatus::OPEN);
    }
}
