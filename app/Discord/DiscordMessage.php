<?php

declare(strict_types=1);

namespace App\Discord;

/**
 * Class DiscordMessage.
 */
class DiscordMessage
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
     * @return DiscordEmbed[]
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
     * @param  array  $embeds
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
    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Convert the constructor to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'channelId' => $this->getChannelId(),
            'id' => $this->getId(),
            'content' => $this->getContent(),
            'embeds' => $this->getEmbeds(),
            'files' => $this->getImages(),
        ];
    }
}
