<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Enums\Actions\Models\Wiki\Video\DiscordNotificationType;
use App\Models\Wiki\Video;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class DiscordVideoNotificationAction
{
    /**
     * Get the HTTP client for Discord API.
     */
    public static function getHttp(): PendingRequest
    {
        return Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->baseUrl(Config::get('services.discord.api_url'));
    }

    /**
     * @param  Collection<int, Video>  $videos
     * @param  array<string, mixed>  $fields
     */
    public function handle(Collection $videos, array $fields): ActionResult
    {
        $type = Arr::get($fields, DiscordNotificationType::getFieldKey());

        static::getHttp()
            ->post('/notification', [
                'type' => $type->value,
                'videos' => $videos->map(fn (Video $video): array => ['videoId' => $video->getKey()])->toArray(),
            ])
            ->throw();

        return new ActionResult(ActionStatus::PASSED);
    }
}
