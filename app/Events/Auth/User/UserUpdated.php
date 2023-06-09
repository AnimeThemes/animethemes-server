<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Enums\Discord\EmbedColor;
use App\Models\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class UserUpdated.
 */
class UserUpdated extends UserEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->initializeEmbedFields($user);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $user = $this->getUser();

        return DiscordMessage::create('', [
            'description' => "User '**{$user->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW->value,
        ]);
    }
}
