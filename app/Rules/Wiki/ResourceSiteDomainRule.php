<?php

declare(strict_types=1);

namespace App\Rules\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ResourceSiteDomainRule.
 */
class ResourceSiteDomainRule implements Rule
{
    /**
     * The resource site key.
     *
     * @var int|null
     */
    protected ?int $site;

    /**
     * Create a new rule instance.
     *
     * @param  int|null  $site
     * @return void
     */
    public function __construct(?int $site)
    {
        $this->site = $site;
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
        $domain = ResourceSite::getDomain($this->site);

        return empty($domain) || $domain === parse_url($value, PHP_URL_HOST);
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
