<?php

declare(strict_types=1);

use App\Rules\Api\EnumLocalizedNameRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\Unit\Enums\LocalizedEnum;

uses(WithFaker::class);

test('passes if enum description', function (): void {
    $enum = Arr::random(LocalizedEnum::cases());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $enum->localize()],
        [$attribute => new EnumLocalizedNameRule(LocalizedEnum::class)]
    );

    $this->assertTrue($validator->passes());
});

test('fails if enum value', function (): void {
    $enum = Arr::random(LocalizedEnum::cases());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $enum->value],
        [$attribute => new EnumLocalizedNameRule(LocalizedEnum::class)]
    );

    $this->assertFalse($validator->passes());
});

test('fails if string', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => Str::random()],
        [$attribute => new EnumLocalizedNameRule(LocalizedEnum::class)]
    );

    $this->assertFalse($validator->passes());
});
