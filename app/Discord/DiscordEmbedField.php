<?php

declare(strict_types=1);

namespace App\Discord;

use BenSampo\Enum\Enum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * Class DiscordEmbedField
 * @package App\Discord
 */
class DiscordEmbedField implements Arrayable, JsonSerializable
{
    public const DEFAULT_FIELD_VALUE = '-';

    /**
     * The name of the field.
     *
     * @var string
     */
    protected string $name;

    /**
     * The value of the field.
     *
     * @var string
     */
    protected string $value;

    /**
     * Whether or not this field should display inline.
     *
     * @var bool
     */
    protected bool $inline;

    /**
     * Create a new field instance.
     *
     * @param string $name
     * @param mixed $value
     * @param bool $inline
     */
    final public function __construct(string $name, mixed $value, bool $inline = false)
    {
        $this->name = $name;
        $this->value = $this->formatEmbedFieldValue($value);
        $this->inline = $inline;
    }

    /**
     * Create a new field instance.
     *
     * @param mixed ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'inline' => $this->inline,
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Format embed value to circumvent exceptions caused by empty or null values.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatEmbedFieldValue(mixed $value): string
    {
        // Use description for enums
        if ($value instanceof Enum) {
            return $value->description;
        }

        // Pretty print booleans
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // Encode to json for all other non-empty scalar values
        if (is_scalar($value) && Str::length($value) > 0) {
            return strval($value);
        }

        return self::DEFAULT_FIELD_VALUE;
    }
}
