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
    public function test_discord_embed_format_enum(): void
    {
        $enum = Arr::random(LocalizedEnum::cases());

        $field = new DiscordEmbedField($this->faker->word(), $enum);

        static::assertEquals($enum->localize(), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format a date value by the Y-m-d date format.
     *
     * @return void
     */
    public function test_discord_embed_format_date(): void
    {
        $date = Date::now()->subDays($this->faker->randomDigitNotNull());

        $field = new DiscordEmbedField($this->faker->word(), $date);

        static::assertEquals($date->format(AllowedDateFormat::YMD->value), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format a boolean value by pretty printing.
     *
     * @return void
     */
    public function test_discord_embed_format_boolean(): void
    {
        $boolean = $this->faker->boolean();

        $field = new DiscordEmbedField($this->faker->word(), $boolean);

        static::assertEquals($boolean ? 'true' : 'false', Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format numbers by their string value.
     *
     * @return void
     */
    public function test_discord_embed_format_number(): void
    {
        $number = $this->faker->randomNumber();

        $field = new DiscordEmbedField($this->faker->word(), $number);

        static::assertEquals(strval($number), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format floats by their string value.
     *
     * @return void
     */
    public function test_discord_embed_format_float(): void
    {
        $float = $this->faker->randomFloat();

        $field = new DiscordEmbedField($this->faker->word(), $float);

        static::assertEquals(strval($float), Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall format non-empty string by their string value.
     *
     * @return void
     */
    public function test_discord_embed_format_string(): void
    {
        $string = $this->faker->word();

        $field = new DiscordEmbedField($this->faker->word(), $string);

        static::assertEquals($string, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for empty string values.
     *
     * @return void
     */
    public function test_discord_embed_format_empty_string(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), '');

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for null values.
     *
     * @return void
     */
    public function test_discord_embed_format_null(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), null);

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for arrays.
     *
     * @return void
     */
    public function test_discord_embed_format_array(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), []);

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }

    /**
     * Discord Embed Fields shall use a default value for objects.
     *
     * @return void
     */
    public function test_discord_embed_format_object(): void
    {
        $field = new DiscordEmbedField($this->faker->word(), new stdClass());

        static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
    }
}
