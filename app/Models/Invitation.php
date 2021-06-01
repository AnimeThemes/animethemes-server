<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Events\Invitation\InvitationCreated;
use App\Events\Invitation\InvitationCreating;
use App\Events\Invitation\InvitationDeleted;
use App\Events\Invitation\InvitationRestored;
use App\Events\Invitation\InvitationUpdated;
use BenSampo\Enum\Traits\CastsEnums;
use Exception;
use ParagonIE\ConstantTime\Base32;

/**
 * Class Invitation
 * @package App\Models
 */
class Invitation extends BaseModel
{
    use CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
     * The attributes that should be cast to enum types.
     *
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Is invitation open?
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status->is(InvitationStatus::OPEN);
    }

    /**
     * Generate token for invitation.
     *
     * @return string
     * @throws Exception
     */
    public static function createToken(): string
    {
        return Base32::encodeUpper(random_bytes(rand(20, 100)));
    }
}
