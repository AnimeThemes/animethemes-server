<?php

declare(strict_types=1);

namespace App\Discord;

/**
 * Class DiscordEmbed.
 */
class DiscordEmbed
{
    final public const ATTRIBUTE_TYPE = 'type';
    final public const ATTRIBUTE_TITLE = 'title';
    final public const ATTRIBUTE_DESCRIPTION = 'description';
    final public const ATTRIBUTE_COLOR = 'color';
    final public const ATTRIBUTE_THUMBNAIL = 'thumbnail';
    final public const ATTRIBUTE_IMAGE = 'image';
    final public const ATTRIBUTE_FIELDS = 'fields';
    final public const ATTRIBUTE_FIELDS_NAME = 'name';
    final public const ATTRIBUTE_FIELDS_VALUE = 'value';
    final public const ATTRIBUTE_FIELDS_INLINE = 'inline';

    protected string $type = 'rich';
    protected string $title = '';
    protected string $description = '';
    protected int $color = 0;
    protected array $thumbnail = [];
    protected array $image = [];
    protected array $fields = [];

    /**
     * Get the type of the embed.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the title of the embed.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the description of the embed.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the color of the embed.
     *
     * @return int
     */
    public function getColor(): int
    {
        return $this->color;
    }

    /**
     * Get the thumbnail of the embed.
     *
     * @return array
     */
    public function getThumbnail(): array
    {
        return $this->thumbnail;
    }

    /**
     * Get the image of the embed.
     *
     * @return array
     */
    public function getImage(): array
    {
        return $this->image;
    }

    /**
     * Get the fields of the embed.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Set the type of the embed.
     *
     * @param  string  $type
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the title of the embed.
     *
     * @param  string  $title
     * @return static
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the description of the embed.
     *
     * @param  string  $description
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the color of the embed.
     *
     * @param  int  $color
     * @return static
     */
    public function setColor(int $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Set the thumbnail of the embed.
     *
     * @param  array  $thumbnail
     * @return static
     */
    public function setThumbnail(array $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Set the image of the embed.
     *
     * @param  array  $image
     * @return static
     */
    public function setImage(array $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Set the fields of the embed.
     *
     * @param  array  $fields
     * @return static
     */
    public function setFields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Convert the embed to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'color' => $this->getColor(),
            'thumbnail' => $this->getThumbnail(),
            'image' => $this->getImage(),
            'fields' => $this->getFields(),
        ];
    }
}
