<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Enums\UserType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ParagonIE\ConstantTime\Base32;

class Invitation extends Model implements Auditable
{

    use CastsEnums;
    use \OwenIt\Auditing\Auditable;

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
            $activity->token = self::createToken();
        });
    }

    public function isOpen() {
        return $this->status->is(InvitationStatus::OPEN);
    }

    public static function createToken() {
        return Base32::encodeUpper(random_bytes(rand(20, 100)));
    }
}
