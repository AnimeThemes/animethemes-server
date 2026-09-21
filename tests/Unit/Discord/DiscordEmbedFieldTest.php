<?php

declare(strict_types=1);

use App\Discord\DiscordEmbedField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Tests\Unit\Enums\LocalizedEnum;

pest()->use(WithFaker::class);

test('discord embed format enum', function (): void {
    $enum = Arr::random(LocalizedEnum::cases());

    $field = new DiscordEmbedField(fake()->word(), $enum);

    expect(Arr::get($field->toArray(), 'value'))->toEqual($enum->localize());
});

test('discord embed format date', function (): void {
    $date = Date::now()->subDays(fake()->randomDigitNotNull());

    $field = new DiscordEmbedField(fake()->word(), $date);

    expect(Arr::get($field->toArray(), 'value'))->toEqual($date->format(AllowedDateFormat::YMD->value));
});

test('discord embed format boolean', function (): void {
    $boolean = fake()->boolean();

    $field = new DiscordEmbedField(fake()->word(), $boolean);

    expect(Arr::get($field->toArray(), 'value'))->toEqual($boolean ? 'true' : 'false');
});

test('discord embed format number', function (): void {
    $number = fake()->randomNumber();

    $field = new DiscordEmbedField(fake()->word(), $number);

    expect(Arr::get($field->toArray(), 'value'))->toEqual(strval($number));
});

test('discord embed format float', function (): void {
    $float = fake()->randomFloat();

    $field = new DiscordEmbedField(fake()->word(), $float);

    expect(Arr::get($field->toArray(), 'value'))->toEqual(strval($float));
});

test('discord embed format string', function (): void {
    $string = fake()->word();

    $field = new DiscordEmbedField(fake()->word(), $string);

    expect(Arr::get($field->toArray(), 'value'))->toEqual($string);
});

test('discord embed format empty string', function (): void {
    $field = new DiscordEmbedField(fake()->word(), '');

    expect(Arr::get($field->toArray(), 'value'))->toEqual(DiscordEmbedField::DEFAULT_NULL_FIELD_VALUE);
});

test('discord embed format null', function (): void {
    $field = new DiscordEmbedField(fake()->word(), null);

    expect(Arr::get($field->toArray(), 'value'))->toEqual(DiscordEmbedField::DEFAULT_NULL_FIELD_VALUE);
});

test('discord embed format array', function (): void {
    $field = new DiscordEmbedField(fake()->word(), []);

    expect(Arr::get($field->toArray(), 'value'))->toEqual(DiscordEmbedField::DEFAULT_NULL_FIELD_VALUE);
});

test('discord embed format object', function (): void {
    $field = new DiscordEmbedField(fake()->word(), new stdClass());

    expect(Arr::get($field->toArray(), 'value'))->toEqual(DiscordEmbedField::DEFAULT_NULL_FIELD_VALUE);
});
