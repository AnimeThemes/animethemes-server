<?php

declare(strict_types=1);

namespace App\Events\Auth\Invitation;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Auth\Invitation;

/**
 * Class InvitationUpdated.
 *
 * @extends AdminUpdatedEvent<Invitation>
 */
class InvitationUpdated extends AdminUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Invitation  $invitation
     */
    public function __construct(Invitation $invitation)
    {
        parent::__construct($invitation);
        $this->initializeEmbedFields($invitation);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Invitation
     */
    public function getModel(): Invitation
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Invitation '**{$this->getModel()->getName()}**' has been updated.";
    }
}
