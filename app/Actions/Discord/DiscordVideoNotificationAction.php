<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Enums\Actions\Models\Wiki\Video\DiscordNotificationType;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DiscordVideoNotificationAction
{
    /**
     * @param  Collection<int, Video>  $videos
     * @param  array<string, mixed>  $fields
     */
    public function handle(Collection $videos, array $fields): ActionResult
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
                if (Str::length($anime->name) >= 100) {
                    $anime->name = Str::limit($anime->name, 96, '...');
                }

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

        DiscordThreadAction::getHttp()
            ->post('/notification', [
                'type' => $type->value,
                'videos' => $newVideos,
            ])
            ->throw();

        return new ActionResult(ActionStatus::PASSED);
    }
}
