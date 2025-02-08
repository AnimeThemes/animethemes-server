<?php

declare(strict_types=1);

namespace App\Events\Base\Pivot;

use App\Enums\Discord\EmbedColor;
use App\Events\BasePivotEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class PivotDeletedEvent.
 *
 * @template TModelRelated of \App\Models\BaseModel
 * @template TModelForeign of \App\Models\BaseModel
 *
 * @extends BasePivotEvent<TModelRelated, TModelForeign>
 */
abstract class PivotDeletedEvent extends BasePivotEvent
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
        $embed = array_merge(
            [
                'description' => $this->getDiscordMessageDescription(),
                'color' => EmbedColor::RED->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
    }
}
