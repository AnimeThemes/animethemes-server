<?php

declare(strict_types=1);

use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use App\Rules\ModerationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails if unknown moderation service', function () {
    $this->expectException(RuntimeException::class);

    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new ModerationRule()],
    );

    $validator->passes();
});

test('fails if flagged by open ai', function () {
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response([
            'results' => [
                0 => [
                    'flagged' => true,
                ],
            ],
        ]),
    ]);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new ModerationRule()],
    );

    $this->assertFalse($validator->passes());
});

test('passes if not flagged by open ai', function () {
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response([
            'results' => [
                0 => [
                    'flagged' => false,
                ],
            ],
        ]),
    ]);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new ModerationRule()],
    );

    $this->assertTrue($validator->passes());
})->only();

test('passes if open ai fails', function () {
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response(status: 404),
    ]);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new ModerationRule()],
    );

    $this->assertTrue($validator->passes());
});
