<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class DiscordThreadAction.
 */
class DiscordThreadAction
{
    /**
     * Handle the action.
     *
     * @param  Anime  $anime
     * @param  array  $fields
     * @return Exception|null
     */
    public function handle(Anime $anime, array $fields): ?Exception
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

            $response = Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
                ->acceptJson()
                ->post(Config::get('services.discord.api_url').'/thread', $animeArray)
                ->throw()
                ->json();

            if (Arr::has($response, 'id')) {
                DiscordThread::query()->create([
                    DiscordThread::ATTRIBUTE_NAME => Arr::get($response, 'name'),
                    DiscordThread::ATTRIBUTE_ID => intval(Arr::get($response, 'id')),
                    DiscordThread::ATTRIBUTE_ANIME => $anime->getKey(),
                ]);
            }

            return null;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return $e;
        }
    }

    /**
     * Get the thread by ID.
     *
     * @param  string  $id
     * @return array
     */
    public function get(string $id): array
    {
        return DiscordMessageAction::getHttp()
            ->acceptJson()
            ->get('/thread', ['id' => $id])
            ->throw()
            ->json();
    }
}
