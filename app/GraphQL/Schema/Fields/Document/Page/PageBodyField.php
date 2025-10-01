<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Document\Page;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Document\Page;

class PageBodyField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Page::ATTRIBUTE_BODY, nullable: false);
    }

    public function description(): string
    {
        return 'The body content of the resource';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'string',
            'max:16777215',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:16777215',
        ];
    }
}
