<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Events\Invitation\InvitationCreated;
use App\Events\Invitation\InvitationCreating;
use App\Events\Invitation\InvitationDeleted;
use App\Events\Invitation\InvitationRestored;
use App\Events\Invitation\InvitationUpdated;
use BenSampo\Enum\Traits\CastsEnums;
use ParagonIE\ConstantTime\Base32;

class Invitation extends BaseModel
{
    use CastsEnums;

    protected $fillable = ['name', 'email', 'status'];

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
        'deleted' => InvitationDeleted::class,
        'restored' => InvitationRestored::class,
        'updated' => InvitationUpdated::class,
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
        'status' => InvitationStatus::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'int',
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
