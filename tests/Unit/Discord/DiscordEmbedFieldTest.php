<?php

declare(strict_types=1);

use App\Discord\DiscordEmbedField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Tests\Unit\Enums\LocalizedEnum;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('discord embed format enum', function () {
    $enum = Arr::random(LocalizedEnum::cases());

    $field = new DiscordEmbedField(fake()->word(), $enum);

    static::assertEquals($enum->localize(), Arr::get($field->toArray(), 'value'));
});

test('discord embed format date', function () {
    $date = Date::now()->subDays(fake()->randomDigitNotNull());

    $field = new DiscordEmbedField(fake()->word(), $date);

    static::assertEquals($date->format(AllowedDateFormat::YMD->value), Arr::get($field->toArray(), 'value'));
});

test('discord embed format boolean', function () {
    $boolean = fake()->boolean();

    $field = new DiscordEmbedField(fake()->word(), $boolean);

    static::assertEquals($boolean ? 'true' : 'false', Arr::get($field->toArray(), 'value'));
});

test('discord embed format number', function () {
    $number = fake()->randomNumber();

    $field = new DiscordEmbedField(fake()->word(), $number);

    static::assertEquals(strval($number), Arr::get($field->toArray(), 'value'));
});

test('discord embed format float', function () {
    $float = fake()->randomFloat();

    $field = new DiscordEmbedField(fake()->word(), $float);

    static::assertEquals(strval($float), Arr::get($field->toArray(), 'value'));
});

test('discord embed format string', function () {
    $string = fake()->word();

    $field = new DiscordEmbedField(fake()->word(), $string);

    static::assertEquals($string, Arr::get($field->toArray(), 'value'));
});

test('discord embed format empty string', function () {
    $field = new DiscordEmbedField(fake()->word(), '');

    static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
});

test('discord embed format null', function () {
    $field = new DiscordEmbedField(fake()->word(), null);

    static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
});

test('discord embed format array', function () {
    $field = new DiscordEmbedField(fake()->word(), []);

    static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
});

test('discord embed format object', function () {
    $field = new DiscordEmbedField(fake()->word(), new stdClass());

    static::assertEquals(DiscordEmbedField::DEFAULT_FIELD_VALUE, Arr::get($field->toArray(), 'value'));
});
