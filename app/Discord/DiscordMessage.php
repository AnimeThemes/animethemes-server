<?php

declare(strict_types=1);

namespace App\Discord;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * Class DiscordMessage.
 */
class DiscordMessage implements Arrayable
{
    final public const ATTRIBUTE_CHANNEL_ID = 'channelId';
    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_URL = 'url';
    final public const ATTRIBUTE_CONTENT = 'content';
    final public const ATTRIBUTE_EMBEDS = 'embeds';
    final public const ATTRIBUTE_IMAGES = 'images';

    protected string $channelId = '0';
    protected string $id = '0';
    protected string $content = '';
    protected array $embeds = [];
    protected array $images = [];

    /**
     * Create a new DiscordMessage instance from an array.
     *
     * @param  array<string, mixed>  $array
     * @return DiscordMessage
     */
    public static function fromArray(array $array): DiscordMessage
    {
        return new DiscordMessage()
            ->setChannelId(Arr::get($array, self::ATTRIBUTE_CHANNEL_ID) ?? '0')
            ->setId(Arr::get($array, self::ATTRIBUTE_ID) ?? '0')
            ->setContent(Arr::get($array, self::ATTRIBUTE_CONTENT) ?? '')
            ->setEmbeds(Arr::map(Arr::get($array, self::ATTRIBUTE_EMBEDS) ?? [], fn (array $embed) => DiscordEmbed::fromArray($embed)))
            ->setImages(Arr::get($array, 'files') ?? Arr::get($array, self::ATTRIBUTE_IMAGES) ?? []);
    }

    /**
     * Get the channelId of the message.
     *
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * Get the id of the message.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the content of the message.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the embeds of the message.
     *
     * @return array<int, DiscordEmbed>
     */
    public function getEmbeds(): array
    {
        return $this->embeds;
    }

    /**
     * Get the images of the message.
     *
     * @return array<string>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Set the content of the message.
     *
     * @param  string  $content
     * @return static
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the embeds of the message.
     *
     * @param  DiscordEmbed[]  $embeds
     * @return static
     */
    public function setEmbeds(array $embeds): static
    {
        $this->embeds = $embeds;

        return $this;
    }

    /**
     * Set the images of the message.
     *
     * @param  array<string>  $images
     * @return static
     */
    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Set the channelId of the message.
     *
     * @param  string  $channelId
     * @return static
     */
    public function setChannelId(string $channelId): static
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * Set the id of the message.
     *
     * @param  string  $id
     * @return static
     */
    public function setId(string $id = '0'): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Convert the constructor to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'channelId' => $this->getChannelId(),
            'id' => $this->getId(),
            'content' => $this->getContent(),
            'embeds' => Arr::map($this->getEmbeds(), fn (DiscordEmbed $embed) => $embed->toArray()),
            'files' => $this->getImages(),
        ];
    }
}
