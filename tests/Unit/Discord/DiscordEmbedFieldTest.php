<?php

declare(strict_types=1);

namespace Tests\Unit\Discord;

use App\Discord\DiscordEmbedField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use stdClass;
use Tests\TestCase;
use Tests\Unit\Enums\LocalizedEnum;

class DiscordEmbedFieldTest extends TestCase
{
    use WithFaker;

    /**
     * Discord Embed Fields shall format an Enum value by its description.
     */
    public function testDiscordEmbedFormatEnum(): void
    {
        $enum = Arr::random(LocalizedEnum::cases());

        $field = new DiscordEmbedField($this->faker->word(), $enum);

        static::assertEquals($enum->localize(), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format a date value by the Y-m-d date format.
     */
    public function testDiscordEmbedFormatDate(): void
    {
        $date = Date::now()->subDays($this->faker->randomDigitNotNull());

        $field = new DiscordEmbedField($this->faker->word(), $date);

        static::assertEquals($date->format(AllowedDateFormat::YMD->value), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format a boolean value by pretty printing.
     */
    public function testDiscordEmbedFormatBoolean(): void
    {
        $boolean = $this->faker->boolean();

        $field = new DiscordEmbedField($this->faker->word(), $boolean);

        static::assertEquals($boolean ? 'true' : 'false', Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format numbers by their string value.
     */
    public function testDiscordEmbedFormatNumber(): void
    {
        $number = $this->faker->randomNumber();

        $field = new DiscordEmbedField($this->faker->word(), $number);

        static::assertEquals(strval($number), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format floats by their string value.
     */
    public function testDiscordEmbedFormatFloat(): void
    {
        $float = $this->faker->randomFloat();

        $field = new DiscordEmbedField($this->faker->word(), $float);

        static::assertEquals(strval($float), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format non-empty string by their string value.
     */
    public function testDiscordEmbedFormatString(): void
    {
        $string = $this->faker->word();

        $field = new DiscordEmbedField($this->faker->word(), $string);

        static::assertEquals($string, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for empty string values.
     */
    public function testDiscordEmbedFormatEmptyString(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), '');

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for null values.
     */
    public function testDiscordEmbedFormatNull(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), null);

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for arrays.
     */
    public function testDiscordEmbedFormatArray(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), []);

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for objects.
     */
    public function testDiscordEmbedFormatObject(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), new stdClass());

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }
}
