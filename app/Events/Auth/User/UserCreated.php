<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class UserCreated.
 */
class UserCreated extends UserEvent
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

        return DiscordMessage::create('', [
            'description' => "User '**{$user->getName()}**' has been created.",
            'color' => EmbedColor::GREEN->value,
        ]);
    }
}
