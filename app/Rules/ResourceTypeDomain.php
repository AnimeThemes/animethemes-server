<?php

namespace App\Rules;

use App\Enums\ResourceType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ResourceTypeDomain implements Rule
{

    private $type;

    /**
     * Create a new rule instance.
     *
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
            $parsed_url = parse_url($value);
            return $domain === $parsed_url['host'];
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.resource_link_site_mismatch');
    }
}
