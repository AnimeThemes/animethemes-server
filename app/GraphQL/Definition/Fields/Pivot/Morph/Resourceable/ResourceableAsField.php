<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Morph\Resourceable;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Pivots\Morph\Resourceable;

class ResourceableAsField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Resourceable::ATTRIBUTE_AS);
    }

    public function description(): string
    {
        return 'Used to distinguish resources that map to the same resourceable';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }
}
