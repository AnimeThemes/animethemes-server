<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ResourceSite;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ResourceSiteDomainRule.
 */
class ResourceSiteDomainRule implements Rule
{
    /**
     * The name of the rule.
     */
    protected string $rule = 'resource_site';

    /**
     * The resource site key.
     *
     * @var int|null
     */
    protected ?int $site;

    /**
     * Create a new rule instance.
     *
     * @param int|null $site
     * @return void
     */
    public function __construct(?int $site)
    {
        $this->site = $site;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $domain = ResourceSite::getDomain($this->site);

        if (! empty($domain)) {
            return $domain === parse_url($value, PHP_URL_HOST);
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message(): array | string | null
    {
        return __('validation.resource_link_site_mismatch');
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     *
     * @see \Illuminate\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString()
    {
        return "{$this->rule}:{$this->site}";
    }
}
