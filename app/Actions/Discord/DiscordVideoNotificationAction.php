<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Enums\Actions\Models\Wiki\Video\DiscordNotificationType;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Class DiscordVideoNotificationAction.
 */
class DiscordVideoNotificationAction
{
    /**
     * Handle the action.
     *
     * @param  Collection<int, Video>  $videos
     * @param  array  $fields
     * @return void
     */
    public function handle(Collection $videos, array $fields): void
    {
        $type = Arr::get($fields, DiscordNotificationType::getFieldKey());

        $newVideos = [];

        foreach ($videos as $video) {
            $video
                ->load([
                    'animethemeentries.animetheme.anime.discordthread',
                    'animethemeentries.animetheme.anime.images',
                    Video::RELATION_GROUP,
                    'animethemeentries.animetheme.song.artists',
                ]);

            $theme = $video->animethemeentries->first()->animetheme;
            $anime = $theme->anime;

            if ($anime->discordthread === null) {

                $threadAction = new DiscordThreadAction();

                $threadAction->handle($anime, ['name' => $anime->getName()]);
                $anime->load('discordthread');
            }

            $videoArray = $video->toArray();

            Arr::set($videoArray, Video::ATTRIBUTE_SOURCE, $video->source->localize());
            Arr::set($videoArray, Video::ATTRIBUTE_OVERLAP, $video->overlap->localize());
            Arr::set($videoArray, 'animethemeentries.0.animetheme.type', $theme->type->localize());

            foreach (Arr::get($videoArray, 'animethemeentries.0.animetheme.anime.images') as $key => $image) {
                Arr::set($videoArray, "animethemeentries.0.animetheme.anime.images.$key.facet", $anime->images->get($key)->facet->localize());
            }

            $newVideos[] = $videoArray;
        }

        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->post(Config::get('services.discord.api_url') . '/notification', [
                'type' => $type->value,
                'videos' => $newVideos,
            ])
            ->throw();
    }
}
