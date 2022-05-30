<?php

declare(strict_types=1);

namespace App\Events\Base;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Events\BaseEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class BaseUpdatedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseEvent<TModel>
 */
abstract class BaseUpdatedEvent extends BaseEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        return DiscordMessage::create('', [
            'description' => $this->getDiscordMessageDescription(),
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW,
        ]);
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    abstract protected function getDiscordMessageDescription(): string;
}
