<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Discord\DiscordThread;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * @extends AdminUpdatedEvent<DiscordThread>
 */
class DiscordThreadUpdated extends AdminUpdatedEvent
{
    public function __construct(DiscordThread $thread)
    {
        parent::__construct($thread);
        $this->updateThread();
        $this->initializeEmbedFields($thread);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): DiscordThread
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been updated.";
    }

    /**
     * Update the thread on discord.
     */
    protected function updateThread(): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->put(Config::get('services.discord.api_url').'/thread', $this->getModel()->toArray())
            ->throw();
    }
}
