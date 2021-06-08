<?php

declare(strict_types=1);

namespace Discord;

use App\Services\Discord\DiscordEmbedField;
use BenSampo\Enum\Enum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use stdClass;
use Tests\TestCase;

/**
 * Class DiscordEmbedFieldTest.
 */
class DiscordEmbedFieldTest extends TestCase
{
    use WithFaker;

    /**
     * Discord Embed Fields shall format an Enum value by its description.
     *
     * @return void
     */
    public function testDiscordEmbedFormatEnum()
    {
        $enum = new class($this->faker->numberBetween(0, 2)) extends Enum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $field = DiscordEmbedField::make($this->faker->word(), $enum);

        static::assertEquals($enum->description, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format a boolean value by pretty printing.
     *
     * @return void
     */
    public function testDiscordEmbedFormatBoolean()
    {
        $boolean = $this->faker->boolean();

        $field = DiscordEmbedField::make($this->faker->word(), $boolean);

        static::assertEquals($boolean ? 'true' : 'false', Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format numbers by their string value.
     *
     * @return void
     */
    public function testDiscordEmbedFormatNumber()
    {
        $number = $this->faker->randomNumber();

        $field = DiscordEmbedField::make($this->faker->word(), $number);

        static::assertEquals(strval($number), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format floats by their string value.
     *
     * @return void
     */
    public function testDiscordEmbedFormatFloat()
    {
        $float = $this->faker->randomFloat();

        $field = DiscordEmbedField::make($this->faker->word(), $float);

        static::assertEquals(strval($float), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format non-empty string by their string value.
     *
     * @return void
     */
    public function testDiscordEmbedFormatString()
    {
        $string = $this->faker->word();

        $field = DiscordEmbedField::make($this->faker->word(), $string);

        static::assertEquals($string, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for empty string values.
     *
     * @return void
     */
    public function testDiscordEmbedFormatEmptyString()
    {
        $field = DiscordEmbedField::make($this->faker->word(), '');

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for null values.
     *
     * @return void
     */
    public function testDiscordEmbedFormatNull()
    {
        $field = DiscordEmbedField::make($this->faker->word(), null);

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for arrays.
     *
     * @return void
     */
    public function testDiscordEmbedFormatArray()
    {
        $field = DiscordEmbedField::make($this->faker->word(), []);

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for objects.
     *
     * @return void
     */
    public function testDiscordEmbedFormatObject()
    {
        $field = DiscordEmbedField::make($this->faker->word(), new stdClass());

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }
}
