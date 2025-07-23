<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class ResourceLinkFormatRule implements DataAwareRule, ValidationRule
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function __construct(protected ?ResourceSite $site = null) {}

    /**
     * Resolve the site.
     */
    protected function site(): ?ResourceSite
    {
        if ($this->site !== null) {
            return $this->site;
        }

        $site = Arr::get($this->data, 'site');
        if (is_numeric($site)) {
            return ResourceSite::tryFrom(intval($site));
        }

        return null;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $site = $this->site();
        if ($site === null) {
            return;
        }

        $key = Str::of($attribute)->explode('.')->last();

        $rules = [
            new AnimeResourceLinkFormatRule($site),
            new ArtistResourceLinkFormatRule($site),
            new SongResourceLinkFormatRule($site),
            new StudioResourceLinkFormatRule($site),
        ];

        foreach ($rules as $rule) {
            $validator = Validator::make(
                [$key => $value],
                [$key => $rule]
            );

            if ($validator->passes()) {
                return;
            }
        }

        $fail(__('validation.regex'));
    }
}
