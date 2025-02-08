<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class UserDeleted.
 */
class UserDeleted extends UserEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $user = $this->getUser();

        $embed = array_merge(
            [
                'description' => "User '**{$user->getName()}**' has been deleted.",
                'color' => EmbedColor::RED->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
    }
}
