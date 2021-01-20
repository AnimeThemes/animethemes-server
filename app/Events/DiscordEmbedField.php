<?php

namespace App\Events;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class DiscordEmbedField implements Arrayable, JsonSerializable
{
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
     * @var boolean
     */
    private $inline;

    /**
     * Create a new field instance.
     *
     * @param string $name
     * @param string $value
     * @param bool $inline
     */
    public function __construct(string $name, string $value, bool $inline = false)
    {
        $this->name = $name;
        $this->value = $value;
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
}
