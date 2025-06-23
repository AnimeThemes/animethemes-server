<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Discord\DiscordMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Class DiscordMessageAction.
 */
class DiscordMessageAction
{
    /**
     * Make the Discord message.
     *
     * @param  array<string, mixed>  $fields
     * @return DiscordMessage
     */
    public function makeMessage(array $fields): DiscordMessage
    {
        $message = DiscordMessage::fromArray($fields);

        if (Arr::has($fields, DiscordMessage::ATTRIBUTE_URL)) {
            $url = Arr::get($fields, DiscordMessage::ATTRIBUTE_URL);
            preg_match('/https:\/\/discord\.com\/channels\/(\d+)\/(\d+)\/(\d+)/', $url, $matches);

            $message
                ->setChannelId(strval($matches[2]))
                ->setId(strval($matches[3]));
        }

        if (Arr::has($fields, DiscordMessage::ATTRIBUTE_CHANNEL_ID)) {
            $message->setChannelId(Arr::get($fields, DiscordMessage::ATTRIBUTE_CHANNEL_ID));
        }

        return $message;
    }

    /**
     * Set the Discord message.
     *
     * @param  string  $url
     * @return DiscordMessage
     */
    public function get(string $url): DiscordMessage
    {
        $message = Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->get(Config::get('services.discord.api_url') . '/message', [
                'url' => $url,
            ])
            ->throw()
            ->json();

        return DiscordMessage::fromArray(Arr::get($message, 'message'));
    }

    /**
     * Edit the Discord message.
     *
     * @param  DiscordMessage  $message
     * @return void
     */
    public function edit(DiscordMessage $message): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->put(Config::get('services.discord.api_url') . '/message', [
                $message->toArray(),
            ])
            ->throw();
    }

    /**
     * Send the Discord message.
     *
     * @param  DiscordMessage  $message
     * @return void
     */
    public function send(DiscordMessage $message): void
    {
        Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->post(Config::get('services.discord.api_url') . '/message', [
                'message' => $message->toArray(),
            ])
            ->throw();
    }
}
