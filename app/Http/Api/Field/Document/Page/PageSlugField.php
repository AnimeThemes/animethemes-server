<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Document\Page;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Document\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageSlugField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Page::ATTRIBUTE_SLUG);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'max:192',
            'regex:/^[\pL\pM\pN\/_-]+$/u',
            Rule::unique(Page::class),
        ];
    }

    /**
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'max:192',
            'regex:/^[\pL\pM\pN\/_-]+$/u',
            Rule::unique(Page::class)->ignore($request->route('page'), Page::ATTRIBUTE_ID),
        ];
    }
}
