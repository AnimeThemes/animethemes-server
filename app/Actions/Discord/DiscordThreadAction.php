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
            $anime->load(Anime::RELATION_IMAGES);

            $anime->name = Arr::get($fields, 'name');

            $animeArray = $anime->toArray();

            Arr::set($animeArray, Anime::ATTRIBUTE_SEASON, $anime->season->localize());
            Arr::set($animeArray, Anime::ATTRIBUTE_MEDIA_FORMAT, $anime->media_format->localize());

            foreach ($animeArray['images'] as $key => $image) {
                Arr::set($animeArray, "images.$key.facet", $anime->images->get($key)->facet->localize());
            }

            $response = static::getHttp()
                ->acceptJson()
                ->post('/thread', $animeArray)
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
