<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Discord\DiscordEmbed;
use App\Discord\DiscordMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Class DiscordMessageAction.
 */
class DiscordMessageAction
{
    protected array $message;

    /**
     * Make the Discord message.
     *
     * @param  array  $fields
     * @return array
     */
    public function makeMessage(array $fields): array
    {
        $message = new DiscordMessage();

        $embeds = [];

        foreach (Arr::get($fields, 'embeds') as $embed) {
            $newEmbed = (new DiscordEmbed())
                ->setTitle(Arr::get($embed, 'title') ?? '')
                ->setDescription(Arr::get($embed, 'description') ?? '')
                ->setColor(hexdec(Arr::get($embed, 'color') ?? ''))
                ->setThumbnail(Arr::get($embed, 'thumbnail'))
                ->setImage(Arr::get($embed, 'image'))
                ->setFields(Arr::get($embed, 'fields') ?? []);

            $embedFields = Arr::get($embed, 'fields');
            $newEmbedFields = [];
            foreach ($embedFields as $embedField) {
                if (Arr::get($embedField, 'name') && Arr::get($embedField, 'value')) {
                    $newEmbedFields[] = $embedField;
                }
            }

            $embeds[] = $newEmbed->setFields($newEmbedFields)->toArray();
        }

        if (Arr::has($fields, 'url')) {
            preg_match('/https:\/\/discord\.com\/channels\/(\d+)\/(\d+)\/(\d+)/', Arr::get($fields, 'url'), $matches);

            $message = $message
                ->setChannelId(strval($matches[2]))
                ->setId(strval($matches[3]));
        }

        if (Arr::has($fields, 'channelId')) {
            $message = $message->setChannelId(Arr::get($fields, 'channelId'));
        }

        $message = $message
            ->setContent(Arr::get($fields, 'content') ?? '')
            ->setEmbeds($embeds)
            ->setFiles(Arr::get($fields, 'images'))
            ->toArray();

        return $message;
    }

    /**
     * Get the current Discord message.
     *
     * @return array
     */
    public function getMessage(): array
    {
        return $this->message;
    }

    /**
     * Set the Discord message.
     *
     * @param  string  $url
     * @return static
     */
    public function get(string $url): static
    {
        $message = Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->get(Config::get('services.discord.api_url') . '/message', [
                'url' => $url,
            ])
            ->throw()
            ->json();

        $this->message = Arr::get($message, 'message');

        return $this;
    }

    /**
     * Edit the Discord message.
     *
     * @param  array  $message
     * @return void
     */
    public function edit(array $message): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->put(Config::get('services.discord.api_url') . '/message', [
                $message,
            ])
            ->throw();
    }

    /**
     * Send the Discord message.
     *
     * @param  array  $message
     * @return void
     */
    public function send(array $message): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->post(Config::get('services.discord.api_url') . '/message', [
                'message' => $message,
            ])
            ->throw();
    }
}
