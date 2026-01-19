<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordThreadAction
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
     * @param  array<string, mixed>  $fields
     *
     * @throws Exception
     */
    public function handle(Anime $anime, array $fields): ActionResult
    {
        try {
            $response = static::getHttp()
                ->acceptJson()
                ->post('/thread', ['name' => Arr::get($fields, 'name'), 'slug' => $anime->slug])
                ->throw()
                ->json();

            if (Arr::has($response, 'id')) {
                DiscordThread::query()->create([
                    DiscordThread::ATTRIBUTE_NAME => Arr::get($response, 'name'),
                    DiscordThread::ATTRIBUTE_ID => intval(Arr::get($response, 'id')),
                    DiscordThread::ATTRIBUTE_ANIME => $anime->getKey(),
                ]);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get the thread by ID.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return static::getHttp()
            ->acceptJson()
            ->get('/thread', ['id' => $id])
            ->throw()
            ->json();
    }
}
