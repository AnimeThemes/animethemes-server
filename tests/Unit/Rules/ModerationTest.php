<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use App\Rules\ModerationRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use WithFaker;

    /**
     * The Moderation Rule shall fail if the moderation service is unknown.
     */
    public function testFailsIfUnknownModerationService(): void
    {
        static::expectException(RuntimeException::class);

        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->word()],
            [$attribute => new ModerationRule()],
        );

        $validator->passes();
    }

    /**
     * The Moderation Rule shall fail if the value is flagged by OpenAI.
     */
    public function testFailsIfFlaggedByOpenAi(): void
    {
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

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->word()],
            [$attribute => new ModerationRule()],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Moderation Rule shall fail if the value is flagged by OpenAI.
     */
    public function testPassesIfNotFlaggedByOpenAi(): void
    {
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

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->word()],
            [$attribute => new ModerationRule()],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Moderation Rule shall fail if OpenAI returns some error.
     */
    public function testPassesIfOpenAiFails(): void
    {
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response(status: 404),
        ]);

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->word()],
            [$attribute => new ModerationRule()],
        );

        static::assertTrue($validator->passes());
    }
}
