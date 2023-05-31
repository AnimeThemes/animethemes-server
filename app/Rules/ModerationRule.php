<?php

declare(strict_types=1);

namespace App\Rules;

use App\Constants\Config\ServiceConstants;
use App\Constants\Config\ValidationConstants;
use App\Enums\Rules\ModerationService;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;
use RuntimeException;

/**
 * Class ModerationRule.
 */
class ModerationRule implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     *
     * @throws RuntimeException
     */
    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        match (Config::get(ValidationConstants::MODERATION_SERVICE_QUALIFIED)) {
            ModerationService::NONE => null,
            ModerationService::OPENAI => $this->invokeForOpenAI($attribute, $value, $fail),
            default => throw new RuntimeException('Invalid moderation service config value'),
        };
    }

    /**
     * Apply content filtering with OpenAI Moderation API.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    private function invokeForOpenAI(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::acceptJson()
                ->withToken(Config::get(ServiceConstants::OPENAI_BEARER_TOKEN))
                ->post('https://api.openai.com/v1/moderations', ['input' => $value])
                ->throw()
                ->json();

            $flagged = Arr::get($response, 'results.0.flagged');

            if (! empty($flagged)) {
                $fail(__('validation.moderation', ['attribute' => $attribute]));
            }
        } catch (Exception $e) {
            // Don't block site functionality if third-party service is down
            Log::error($e->getMessage(), ['value' => $value]);
        }
    }
}
