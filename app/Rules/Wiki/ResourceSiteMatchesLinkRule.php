<?php

declare(strict_types=1);

namespace App\Rules\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ResourceSiteMatchesLinkRule.
 */
class ResourceSiteMatchesLinkRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param  string  $link
     * @return void
     */
    public function __construct(protected readonly string $link)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $domain = ResourceSite::getDomain($value);

        return empty($domain) || $domain === parse_url($this->link, PHP_URL_HOST);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.resource_link_site_mismatch');
    }
}
