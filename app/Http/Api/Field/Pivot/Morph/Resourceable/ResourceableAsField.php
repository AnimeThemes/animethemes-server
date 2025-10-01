<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Morph\Resourceable;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Pivots\Morph\Resourceable;
use Illuminate\Http\Request;

class ResourceableAsField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Resourceable::ATTRIBUTE_AS);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }
}
