<?php

namespace Tests\Unit\Discord;

use App\Discord\DiscordEmbedField;
use BenSampo\Enum\Enum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

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
        $enum = new class($this->faker->numberBetween(0, 2)) extends Enum {
            const ZERO = 0;
            const ONE = 1;
            const TWO = 2;
        };

        $field = DiscordEmbedField::make($this->faker->word(), $enum);

        $this->assertEquals($enum->description, Arr::get($field->toArray(), 'value'));
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

        $this->assertEquals($boolean ? 'true' : 'false', Arr::get($field->toArray(), 'value'));
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

        $this->assertEquals(strval($number), Arr::get($field->toArray(), 'value'));
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

        $this->assertEquals(strval($float), Arr::get($field->toArray(), 'value'));
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

        $this->assertEquals(strval($string), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for empty string values.
     *
     * @return void
     */
    public function testDiscordEmbedFormatEmptyString()
    {
        $field = DiscordEmbedField::make($this->faker->word(), '');

        $this->assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for null values.
     *
     * @return void
     */
    public function testDiscordEmbedFormatNull()
    {
        $field = DiscordEmbedField::make($this->faker->word(), null);

        $this->assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for arrays.
     *
     * @return void
     */
    public function testDiscordEmbedFormatArray()
    {
        $field = DiscordEmbedField::make($this->faker->word(), []);

        $this->assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for objects.
     *
     * @return void
     */
    public function testDiscordEmbedFormatObject()
    {
        $field = DiscordEmbedField::make($this->faker->word(), new \stdClass());

        $this->assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }
}
