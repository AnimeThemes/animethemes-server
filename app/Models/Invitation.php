<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Enums\UserRole;
use App\Events\Invitation\InvitationCreated;
use App\Events\Invitation\InvitationCreating;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ParagonIE\ConstantTime\Base32;

class Invitation extends Model implements Auditable
{
    use CastsEnums, HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => InvitationCreated::class,
        'creating' => InvitationCreating::class,
    ];

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

    /**
     * @var array
     */
    protected $enumCasts = [
        'role' => UserRole::class,
        'status' => InvitationStatus::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role' => 'int',
        'status' => 'int',
    ];

    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->status->is(InvitationStatus::OPEN);
    }

    /**
     * @return string
     */
    public static function createToken()
    {
        return Base32::encodeUpper(random_bytes(rand(20, 100)));
    }
}
