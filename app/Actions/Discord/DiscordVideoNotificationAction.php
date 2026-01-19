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
                ]);

            $anime = $video->animethemeentries->first()->animetheme->anime;

            if ($anime->discordthread === null) {
                if (Str::length($anime->name) >= 100) {
                    $anime->name = Str::limit($anime->name, 96, '...');
                }

                $threadAction = new DiscordThreadAction();

                $threadAction->handle($anime, ['name' => $anime->getName()]);
                $anime->load('discordthread');
            }

            $newVideos[] = [
                'threadId' => $anime->discordthread->getKey(),
                'videoId' => $video->getKey(),
            ];
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
