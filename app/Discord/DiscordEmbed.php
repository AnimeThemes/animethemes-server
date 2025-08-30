<?php

declare(strict_types=1);

namespace App\Discord;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class DiscordEmbed implements Arrayable
{
    final public const ATTRIBUTE_TYPE = 'type';
    final public const ATTRIBUTE_TITLE = 'title';
    final public const ATTRIBUTE_DESCRIPTION = 'description';
    final public const ATTRIBUTE_COLOR = 'color';
    final public const ATTRIBUTE_THUMBNAIL = 'thumbnail';
    final public const ATTRIBUTE_IMAGE = 'image';
    final public const ATTRIBUTE_FIELDS = 'fields';

    protected string $type = 'rich';
    protected string $title = '';
    protected string $description = '';
    protected int $color = 0;
    protected array $thumbnail = [];
    protected array $image = [];
    protected array $fields = [];

    /**
     * Create a new DiscordEmbed instance from an array.
     *
     * @param  array<string, mixed>  $array
     */
    public static function from(array $array): DiscordEmbed
    {
        return new DiscordEmbed()
            ->setType(Arr::get($array, self::ATTRIBUTE_TYPE) ?? 'rich')
            ->setTitle(Arr::get($array, self::ATTRIBUTE_TITLE) ?? '')
            ->setDescription(Arr::get($array, self::ATTRIBUTE_DESCRIPTION, ''))
            ->setColor(Arr::get($array, self::ATTRIBUTE_COLOR) ?? 0)
            ->setThumbnail(Arr::get($array, self::ATTRIBUTE_THUMBNAIL) ?? [])
            ->setImage(Arr::get($array, self::ATTRIBUTE_IMAGE) ?? [])
            ->setFields(Arr::map(Arr::get($array, self::ATTRIBUTE_FIELDS) ?? [], fn (array $fields) => DiscordEmbedField::from($fields)));
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getColor(): int
    {
        return $this->color;
    }

    /**
     * @return array
     */
    public function getThumbnail(): array
    {
        return $this->thumbnail;
    }

    /**
     * @return array
     */
    public function getImage(): array
    {
        return $this->image;
    }

    /**
     * @return DiscordEmbedField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setColor(int|string $color): static
    {
        $this->color = is_string($color) ? hexdec($color) : $color;

        return $this;
    }

    public function setThumbnail(array $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param  array  $image
     */
    public function setImage(array $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @param  DiscordEmbedField[]  $fields
     */
    public function setFields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array<string, mixed>
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
            'fields' => Arr::map($this->getFields(), fn (DiscordEmbedField $field) => $field->toArray(false)),
        ];
    }
}
