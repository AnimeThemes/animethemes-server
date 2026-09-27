<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class ModelResourceLinkFormatRule implements ValidationRule
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected ResourceSite $site, protected string $model) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = $this->site->getPattern($this->model);

        if ($pattern !== null && Str::match($pattern, $value) !== $value) {
            $fail(__('validation.regex'));
        }
    }
}
