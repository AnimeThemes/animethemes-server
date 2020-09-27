<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Enums\UserType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Invitation extends Model implements Auditable
{

    use CastsEnums, HasFactory;
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

    /**
     * @var array
     */
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

    /**
     * @return boolean
     */
    public function isOpen() : bool {
        return $this->status->is(InvitationStatus::OPEN);
    }
}
