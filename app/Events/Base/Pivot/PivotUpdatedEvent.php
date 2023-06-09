<?php

declare(strict_types=1);

namespace App\Events\Base\Pivot;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Enums\Discord\EmbedColor;
use App\Events\BasePivotEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class PivotUpdatedEvent.
 *
 * @template TModelRelated of \App\Models\BaseModel
 * @template TModelForeign of \App\Models\BaseModel
 *
 * @extends BasePivotEvent<TModelRelated, TModelForeign>
 */
abstract class PivotUpdatedEvent extends BasePivotEvent
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
            'color' => EmbedColor::YELLOW->value,
        ]);
    }
}
