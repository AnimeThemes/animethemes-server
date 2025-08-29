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

    public function getModel(): DiscordThread
    {
        return $this->model;
    }

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
