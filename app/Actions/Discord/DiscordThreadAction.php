<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DiscordThreadAction
{
    /**
     * Handle the action.
     *
     * @param  Anime  $anime
     * @param  array  $fields
     *
     * @return void
     */
    public function handle(Anime $anime, array $fields): void
    {
        $anime->load(Anime::RELATION_IMAGES);

        $anime->name = Arr::get($fields, 'name');

        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $fs = Storage::disk(Config::get('image.disk'));

        $anime->images->each(fn ($image) => Arr::set($image, 'link', $fs->url($image->path)));

        $thread = Http::post(Config::get('services.discord.api_url') . '/thread', $anime->toArray())
            ->json()
            ->throw();

        if ($thread->status() === 201) {
            $newThread = new DiscordThread([
                DiscordThread::ATTRIBUTE_ANIME => $anime->getKey(),
                DiscordThread::ATTRIBUTE_NAME => Arr::get($thread, 'data.name'),
                DiscordThread::ATTRIBUTE_ID => Arr::get($thread, 'data.id'),
            ]);
    
            $newThread->save();
        }
    }
}
