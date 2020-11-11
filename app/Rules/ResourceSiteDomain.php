<?php

namespace App\Rules;

use App\Enums\ResourceSite;
use Illuminate\Contracts\Validation\Rule;

class ResourceSiteDomain implements Rule
{
    /**
     * The resource site key.
     *
     * @var int
     */
    private $site;

    /**
     * Create a new rule instance.
     *
     * @param  int $site The resource site key
     * @return void
     */
    public function __construct($site)
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
    public function passes($attribute, $value)
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
    public function message()
    {
        return __('validation.resource_link_site_mismatch');
    }
}
