<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Discord\DiscordThread;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Class DiscordThreadDeleted.
 *
 * @extends AdminDeletedEvent<DiscordThread>
 */
class DiscordThreadDeleted extends AdminDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  DiscordThread  $thread
     */
    public function __construct(DiscordThread $thread)
    {
        parent::__construct($thread);
        $this->deleteThread();
    }

    /**
     * Get the model that has fired this event.
     *
     * @return DiscordThread
     */
    public function getModel(): DiscordThread
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Delete the thread on discord.
     *
     * @return void
     */
    protected function deleteThread(): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->delete(Config::get('services.discord.api_url').'/thread', ['id' => $this->getModel()->getKey()])
            ->throw();
    }
}
