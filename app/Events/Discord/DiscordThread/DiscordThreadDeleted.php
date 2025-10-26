<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Discord\DiscordThread;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * @extends AdminDeletedEvent<DiscordThread>
 */
class DiscordThreadDeleted extends AdminDeletedEvent
{
    public function __construct(DiscordThread $thread)
    {
        parent::__construct($thread);
        $this->deleteThread();
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Delete the thread on discord.
     */
    protected function deleteThread(): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->delete(Config::get('services.discord.api_url').'/thread', ['id' => $this->getModel()->getKey()])
            ->throw();
    }
}
