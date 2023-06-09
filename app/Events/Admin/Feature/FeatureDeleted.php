<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class FeatureDeleted.
 */
class FeatureDeleted extends FeatureEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $feature = $this->getFeature();

        return DiscordMessage::create('', [
            'description' => "Feature '**{$feature->getName()}**' has been deleted.",
            'color' => EmbedColor::RED->value,
        ]);
    }
}
