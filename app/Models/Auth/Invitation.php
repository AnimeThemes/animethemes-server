<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Events\Auth\Invitation\InvitationCreated;
use App\Events\Auth\Invitation\InvitationDeleted;
use App\Events\Auth\Invitation\InvitationRestored;
use App\Events\Auth\Invitation\InvitationUpdated;
use App\Models\BaseModel;
use BenSampo\Enum\Enum;
use Database\Factories\Auth\InvitationFactory;

/**
 * Class Invitation.
 *
 * @property string $email
 * @property int $invitation_id
 * @property string $name
 * @property Enum $status
 *
 * @method static InvitationFactory factory(...$parameters)
 */
class Invitation extends BaseModel
{
    public const TABLE = 'invitations';

    public const ATTRIBUTE_EMAIL = 'email';
    public const ATTRIBUTE_ID = 'invitation_id';
    public const ATTRIBUTE_NAME = 'name';
    public const ATTRIBUTE_STATUS = 'status';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Invitation::ATTRIBUTE_EMAIL,
        Invitation::ATTRIBUTE_NAME,
        Invitation::ATTRIBUTE_STATUS,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => InvitationCreated::class,
        'deleted' => InvitationDeleted::class,
        'restored' => InvitationRestored::class,
        'updated' => InvitationUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Invitation::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Invitation::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        Invitation::ATTRIBUTE_STATUS => InvitationStatus::class,
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
}
