<?php

declare(strict_types=1);

namespace App\Actions\Discord;

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

        $anime->images->each(fn ($image) => $image->link = $fs->url($image->path));

        Http::post(Config::get('services.discord.api_url') . '/thread', $anime->toArray())
            ->throw();
    }
}
