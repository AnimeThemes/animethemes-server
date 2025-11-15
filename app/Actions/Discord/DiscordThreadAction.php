<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DiscordThreadAction
{
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

            $response = DiscordMessageAction::getHttp()
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
        return DiscordMessageAction::getHttp()
            ->acceptJson()
            ->get('/thread', ['id' => $id])
            ->throw()
            ->json();
    }
}
