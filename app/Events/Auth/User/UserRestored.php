<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class UserRestored.
 */
class UserRestored extends UserEvent
{
    use Dispatchable;
    use SerializesModels;

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
                'description' => "User '**{$user->getName()}**' has been restored.",
                'color' => EmbedColor::GREEN->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
    }
}
