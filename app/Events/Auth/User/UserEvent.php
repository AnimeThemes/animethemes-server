<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Config;

/**
 * Class UserEvent.
 */
abstract class UserEvent implements DiscordMessageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(protected User $user)
    {
    }

    /**
     * Get the user that has fired this event.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     */
    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }
}
