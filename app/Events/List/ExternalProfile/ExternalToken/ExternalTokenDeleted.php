<?php

declare(strict_types=1);

namespace App\Events\List\ExternalProfile\ExternalToken;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalToken;

/**
 * Class ExternalTokenDeleted.
 *
 * @extends ListDeletedEvent<ExternalToken>
 */
class ExternalTokenDeleted extends ListDeletedEvent
{
    /**
     * The profile the token belongs to.
     *
     * @var ExternalProfile
     */
    protected ExternalProfile $profile;

    /**
     * Create a new event instance.
     *
     * @param  ExternalToken  $token
     */
    public function __construct(ExternalToken $token)
    {
        parent::__construct($token);
        $this->profile = $token->externalprofile;
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    /**
     * Get the model that has fired this event.
     *
     * @return ExternalToken
     */
    public function getModel(): ExternalToken
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
        return "Token '**{$this->getModel()->getName()}**' has been deleted for External Profile '**{$this->profile->getName()}**'.";
    }
}
