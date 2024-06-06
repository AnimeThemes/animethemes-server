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

        foreach (Arr::get($fields, DiscordMessage::ATTRIBUTE_EMBEDS) as $embed) {
            $newEmbed = (new DiscordEmbed())
                ->setTitle(Arr::get($embed, DiscordEmbed::ATTRIBUTE_TITLE) ?? '')
                ->setDescription(Arr::get($embed, DiscordEmbed::ATTRIBUTE_DESCRIPTION) ?? '')
                ->setColor(hexdec(Arr::get($embed, DiscordEmbed::ATTRIBUTE_COLOR) ?? ''))
                ->setThumbnail(Arr::get($embed, DiscordEmbed::ATTRIBUTE_THUMBNAIL))
                ->setImage(Arr::get($embed, DiscordEmbed::ATTRIBUTE_IMAGE))
                ->setFields(Arr::get($embed, DiscordEmbed::ATTRIBUTE_FIELDS) ?? []);

            $embedFields = Arr::get($embed, DiscordEmbed::ATTRIBUTE_FIELDS);
            $newEmbedFields = [];
            foreach ($embedFields as $embedField) {
                if (Arr::get($embedField, DiscordEmbed::ATTRIBUTE_FIELDS_NAME) && Arr::get($embedField, DiscordEmbed::ATTRIBUTE_FIELDS_VALUE)) {
                    $newEmbedFields[] = $embedField;
                }
            }

            $embeds[] = $newEmbed->setFields($newEmbedFields)->toArray();
        }

        if (Arr::has($fields, DiscordMessage::ATTRIBUTE_URL)) {
            preg_match('/https:\/\/discord\.com\/channels\/(\d+)\/(\d+)\/(\d+)/', Arr::get($fields, DiscordMessage::ATTRIBUTE_URL), $matches);

            $message = $message
                ->setChannelId(strval($matches[2]))
                ->setId(strval($matches[3]));
        }

        if (Arr::has($fields, DiscordMessage::ATTRIBUTE_CHANNEL_ID)) {
            $message = $message->setChannelId(Arr::get($fields, DiscordMessage::ATTRIBUTE_CHANNEL_ID));
        }

        $message = $message
            ->setContent(Arr::get($fields, DiscordMessage::ATTRIBUTE_CONTENT) ?? '')
            ->setEmbeds($embeds)
            ->setImages(Arr::get($fields, DiscordMessage::ATTRIBUTE_IMAGES))
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
