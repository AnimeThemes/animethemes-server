<?php

declare(strict_types=1);

namespace App\Events\Admin\Setting;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SettingDeleted.
 */
class SettingDeleted extends SettingEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $setting = $this->getSetting();

        return DiscordMessage::create('', [
            'description' => "Setting '**{$setting->getName()}**' has been deleted.",
            'color' => EmbedColor::RED,
        ]);
    }
}
