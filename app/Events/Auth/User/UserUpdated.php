<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Auth\User;
use App\Services\Discord\DiscordEmbedField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class UserUpdated.
 */
class UserUpdated extends UserEvent implements DiscordMessageEvent
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
            'color' => EmbedColor::YELLOW,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.admin_discord_channel');
    }

    /**
     * Add Embed Fields.
     *
     * @param  Model  $original
     * @param  Model  $changed
     * @param  Collection  $changedAttributes
     * @return void
     */
    protected function addEmbedFields(Model $original, Model $changed, Collection $changedAttributes): void
    {
        foreach ($changedAttributes as $attribute) {
            $this->addEmbedField(DiscordEmbedField::make('Attribute', $attribute, true));
            if ($attribute === 'password') {
                $this->addEmbedField(DiscordEmbedField::make('Old', null, true));
                $this->addEmbedField(DiscordEmbedField::make('New', null, true));
            } else {
                $this->addEmbedField(DiscordEmbedField::make('Old', $original->getAttribute($attribute), true));
                $this->addEmbedField(DiscordEmbedField::make('New', $changed->getAttribute($attribute), true));
            }
        }
    }
}
