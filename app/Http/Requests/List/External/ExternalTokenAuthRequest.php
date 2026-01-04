<?php

declare(strict_types=1);

namespace App\Http\Requests\List\External;

use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\ExternalProfile;
use App\Rules\Api\EnumLocalizedNameRule;
use Illuminate\Foundation\Http\FormRequest;

class ExternalTokenAuthRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ExternalProfile::ATTRIBUTE_SITE => [
                'required',
                new EnumLocalizedNameRule(ExternalProfileSite::class),
            ],
        ];
    }
}
