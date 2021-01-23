<?php

namespace App\Discord\Embed;

use BenSampo\Enum\Enum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

class DiscordEmbedField implements Arrayable, JsonSerializable
{
    const DEFAULT_FIELD_VALUE = '-';

    /**
     * The name of the field.
     *
     * @var string
     */
    private $name;

    /**
     * The value of the field.
     *
     * @var string
     */
    private $value;

    /**
     * Whether or not this field should display inline.
     *
     * @var bool
     */
    private $inline;

    /**
     * Create a new field instance.
     *
     * @param string $name
     * @param mixed $value
     * @param bool $inline
     */
    final public function __construct(string $name, $value, bool $inline = false)
    {
        $this->name = $name;
        $this->value = $this->formatEmbedFieldValue($value);
        $this->inline = $inline;
    }

    /**
     * Create a new field instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Format embed value to circumvent exceptions caused by empty or null values.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatEmbedFieldValue($value)
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
