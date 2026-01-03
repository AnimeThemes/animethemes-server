<?php

declare(strict_types=1);

namespace App\Http\Requests\List\External;

use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\ExternalProfile;
use App\Rules\Api\EnumLocalizedNameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExternalTokenCallbackRequest extends FormRequest
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
            'code' => [
                'sometimes',
                Rule::when(
                    $this->get(ExternalProfile::ATTRIBUTE_SITE) === ExternalProfileSite::MAL->localize()
                    || $this->get(ExternalProfile::ATTRIBUTE_SITE) === ExternalProfileSite::ANILIST->localize(),
                    ['required']
                ),
                'string',
            ],
            'state' => [
                Rule::when(
                    $this->get(ExternalProfile::ATTRIBUTE_SITE) === ExternalProfileSite::MAL->localize(),
                    ['required']
                ),
                'string',
            ],
        ];
    }
}
