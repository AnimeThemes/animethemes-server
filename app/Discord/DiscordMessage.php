<?php

declare(strict_types=1);

namespace App\Discord;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class DiscordMessage implements Arrayable
{
    final public const string ATTRIBUTE_CHANNEL_ID = 'channelId';
    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_URL = 'url';
    final public const string ATTRIBUTE_CONTENT = 'content';
    final public const string ATTRIBUTE_EMBEDS = 'embeds';
    final public const string ATTRIBUTE_IMAGES = 'images';

    protected string $channelId = '0';
    protected string $id = '0';
    protected string $content = '';
    protected array $embeds = [];
    protected array $images = [];

    /**
     * @param  array<string, mixed>  $array
     */
    public static function from(array $array): DiscordMessage
    {
        return new DiscordMessage()
            ->setChannelId(Arr::get($array, self::ATTRIBUTE_CHANNEL_ID) ?? '0')
            ->setId(Arr::get($array, self::ATTRIBUTE_ID) ?? '0')
            ->setContent(Arr::get($array, self::ATTRIBUTE_CONTENT) ?? '')
            ->setEmbeds(Arr::map(Arr::get($array, self::ATTRIBUTE_EMBEDS) ?? [], fn (array $embed): DiscordEmbed => DiscordEmbed::from($embed)))
            ->setImages(Arr::get($array, 'files') ?? Arr::get($array, self::ATTRIBUTE_IMAGES) ?? []);
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return DiscordEmbed[]
     */
    public function getEmbeds(): array
    {
        return $this->embeds;
    }

    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param  DiscordEmbed[]  $embeds
     */
    public function setEmbeds(array $embeds): static
    {
        $this->embeds = $embeds;

        return $this;
    }

    /**
     * @param  string[]  $images
     */
    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function setChannelId(string $channelId): static
    {
        $this->channelId = $channelId;

        return $this;
    }

    public function setId(string $id = '0'): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'channelId' => $this->getChannelId(),
            'id' => $this->getId(),
            'content' => $this->getContent(),
            'embeds' => Arr::map($this->getEmbeds(), fn (DiscordEmbed $embed): array => $embed->toArray()),
            'files' => $this->getImages(),
        ];
    }
}
