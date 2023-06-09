<?php

declare(strict_types=1);

namespace App\Events\Admin\Setting;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SettingCreated.
 */
class SettingCreated extends SettingEvent
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
        $setting = $this->getSetting();

        return DiscordMessage::create('', [
            'description' => "Setting '**{$setting->getName()}**' has been created.",
            'color' => EmbedColor::GREEN->value,
        ]);
    }
}
