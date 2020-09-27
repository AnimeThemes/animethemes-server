<?php

namespace App\Rules;

use App\Enums\ResourceType;
use Illuminate\Contracts\Validation\Rule;

class ResourceTypeDomain implements Rule
{

    /**
     * The resource type key
     *
     * @var integer
     */
    private $type;

    /**
     * Create a new rule instance.
     *
     * @param  integer $type The resource type key
     * @return void
     */
    public function __construct($type)
    {
        $this->type = $type;
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
        $domain = ResourceType::getDomain($this->type);

        if (!empty($domain)) {
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
