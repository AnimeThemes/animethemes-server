<?php

declare(strict_types=1);

namespace App\Events\Base\Pivot;

use App\Enums\Discord\EmbedColor;
use App\Events\BasePivotEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class PivotCreatedEvent.
 *
 * @template TModelRelated of \App\Models\BaseModel
 * @template TModelForeign of \App\Models\BaseModel
 * @extends BasePivotEvent<TModelRelated, TModelForeign>
 */
abstract class PivotCreatedEvent extends BasePivotEvent
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
        return DiscordMessage::create('', [
            'description' => $this->getDiscordMessageDescription(),
            'color' => EmbedColor::GREEN,
        ]);
    }
}
