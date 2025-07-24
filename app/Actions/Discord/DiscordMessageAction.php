<?php

declare(strict_types=1);

namespace App\Actions\Discord;

use App\Discord\DiscordMessage;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class DiscordMessageAction
{
    /**
     * Get the HTTP client for Discord API.
     */
    public static function getHttp(): PendingRequest
    {
        return Http::withHeaders(['x-api-key' => Config::get('services.discord.api_key')])
            ->baseUrl(Config::get('services.discord.api_url'));
    }

    /**
     * Make the Discord message.
     *
     * @param  array<string, mixed>  $fields
     */
    public function makeMessage(array $fields): DiscordMessage
    {
        $message = DiscordMessage::from($fields);

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
     */
    public function get(string $url): DiscordMessage
    {
        $message = static::getHttp()
            ->get('/message', [
                'url' => $url,
            ])
            ->throw()
            ->json();

        return DiscordMessage::from(Arr::get($message, 'message'));
    }

    /**
     * Edit the Discord message.
     */
    public function edit(DiscordMessage $message): void
    {
        static::getHttp()
            ->put('/message', [
                $message->toArray(),
            ])
            ->throw();
    }

    /**
     * Send the Discord message.
     */
    public function send(DiscordMessage $message): void
    {
        static::getHttp()
            ->post('/message', [
                'message' => $message->toArray(),
            ])
            ->throw();
    }
}
