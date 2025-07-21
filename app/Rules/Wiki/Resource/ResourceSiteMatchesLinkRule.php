<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Uri;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class ResourceSiteMatchesLinkRule.
 */
readonly class ResourceSiteMatchesLinkRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  string  $link
     * @return void
     */
    public function __construct(protected string $link) {}

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = ResourceSite::getDomain($value);

        if (! empty($domain) && $domain !== Uri::of($this->link)->host()) {
            $fail(__('validation.resource_link_site_mismatch'));
        }
    }
}
