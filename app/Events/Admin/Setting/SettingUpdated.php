<?php

declare(strict_types=1);

namespace App\Events\Admin\Setting;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Enums\Discord\EmbedColor;
use App\Models\Admin\Setting;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SettingUpdated.
 */
class SettingUpdated extends SettingEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  Setting  $setting
     * @return void
     */
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
        $this->initializeEmbedFields($setting);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $setting = $this->getSetting();

        return DiscordMessage::create('', [
            'description' => "Setting '**{$setting->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW->value,
        ]);
    }
}
