<?php

declare(strict_types=1);

namespace App\Discord;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * Class DiscordEmbedField.
 */
class DiscordEmbedField implements Arrayable, JsonSerializable
{
    final public const DEFAULT_FIELD_VALUE = '-';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_VALUE = 'value';
    final public const ATTRIBUTE_INLINE = 'inline';

    /**
     * The formatted value of the field.
     *
     * @var string
     */
    protected readonly string $formattedValue;

    /**
     * Create a new field instance.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool  $inline
     */
    final public function __construct(
        protected readonly string $name,
        protected readonly mixed $value,
        protected readonly bool $inline = false
    ) {
        $this->formattedValue = static::formatEmbedFieldValue($value);
    }

    /**
     * Create a new DiscordEmbedField instance from an array.
     *
     * @param  array<string, mixed>  $array
     * @return static
     */
    public static function fromArray(array $array): static
    {
        return new static(
            Arr::get($array, 'name'),
            Arr::get($array, 'value'),
            Arr::get($array, 'inline'),
        );
    }

    /**
     * Get the instance as an array.
     *
     * @param  bool  $formatted
     * @return array<string, mixed>
     */
    public function toArray(bool $formatted = true): array
    {
        return [
            'name' => $this->name,
            'value' => $formatted ? $this->formattedValue : $this->value,
            'inline' => $this->inline,
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Format embed value to circumvent exceptions caused by empty or null values.
     *
     * @param  mixed  $value
     * @return string
     */
    public static function formatEmbedFieldValue(mixed $value): string
    {
        // Use description for enums
        if (is_object($value) && enum_exists(get_class($value))) {
            return $value->localize();
        }

        // Use 'Y-m-d' format for dates
        if ($value instanceof Carbon) {
            return $value->format(AllowedDateFormat::YMD->value);
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
