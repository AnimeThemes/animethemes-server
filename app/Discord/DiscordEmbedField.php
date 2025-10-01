<?php

declare(strict_types=1);

namespace App\Discord;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JsonSerializable;

class DiscordEmbedField implements Arrayable, JsonSerializable
{
    final public const string DEFAULT_FIELD_VALUE = '-';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_VALUE = 'value';
    final public const string ATTRIBUTE_INLINE = 'inline';

    protected readonly string $formattedValue;

    final public function __construct(
        protected readonly string $name,
        protected readonly mixed $value,
        protected readonly bool $inline = false
    ) {
        $this->formattedValue = static::formatEmbedFieldValue($value);
    }

    /**
     * @param  array<string, mixed>  $array
     */
    public static function from(array $array): static
    {
        return new static(
            Arr::get($array, 'name'),
            Arr::get($array, 'value'),
            Arr::get($array, 'inline'),
        );
    }

    /**
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
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function formatEmbedFieldValue(mixed $value): string
    {
        // Use description for enums
        if (is_object($value) && enum_exists($value::class)) {
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
