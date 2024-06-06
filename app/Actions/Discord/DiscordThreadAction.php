<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

        $response = Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->acceptJson()
            ->post(Config::get('services.discord.api_url') . '/thread', $anime->toArray())
            ->throw()
            ->json();

        if (Arr::has($response, 'id')) {
            DiscordThread::query()->create([
                DiscordThread::ATTRIBUTE_NAME => Arr::get($response, 'name'),
                DiscordThread::ATTRIBUTE_ID => intval(Arr::get($response, 'id')),
                DiscordThread::ATTRIBUTE_ANIME => $anime->getKey(),
            ]);
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
        return Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->acceptJson()
            ->get(Config::get('services.discord.api_url') . '/thread', ['id' => $id])
            ->throw()
            ->json();
    }
}
