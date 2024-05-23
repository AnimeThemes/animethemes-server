<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Constants\Config\ServiceConstants;
use App\Enums\Discord\EmbedColor;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
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
        $name = Arr::get($fields, 'name');

        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $imageDisk = Storage::disk(Config::get('image.disk'));

        $imagePath = $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->first()->path;
        
        $animepage = json_decode(file_get_contents(base_path('composer.json')), true)['homepage'].'anime/';
        $description = '**Synopsis:** '.strip_tags($anime->synopsis)."\n\n".'**Link:** '.$animepage.$anime->slug;

        Http::withToken(Config::get('services.discord.token'), 'Bot')
            ->asMultipart()
            ->attach('file', file_get_contents($imageDisk->url($imagePath)), 'image.jpg')
            ->post("https://discord.com/api/v10/channels/{$this->getDiscordChannel()}/threads", [
                'payload_json' => json_encode([
                    'name' => $name,
                    'applied_tags' => $this->getAppliedTags($anime->season->value),
                    'message' => [
                        'embeds' => [
                            [
                                'color' => EmbedColor::PURPLE->value,
                                'title' => $anime->name,
                                'description' => $description,
                            ]
                        ],
                    ]
                ])
            ])->throw();
    }

    /**
     * Get Discord forum channel the thread will be created to.
     *
     * @return string
     */
    protected function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::SUBMISSIONS_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Get the IDs of the tags applied to the thread.
     * 
     * @param  int  $season
     * @return array
     */
    protected function getAppliedTags(int $season): array
    {
        return match ($season) {
            AnimeSeason::WINTER->value => [Config::get('services.discord.submissions_forum_tags.winter')],
            AnimeSeason::SPRING->value => [Config::get('services.discord.submissions_forum_tags.spring')],
            AnimeSeason::SUMMER->value => [Config::get('services.discord.submissions_forum_tags.summer')],
            AnimeSeason::FALL->value => [Config::get('services.discord.submissions_forum_tags.fall')],
            default => [],
        };
    }
}