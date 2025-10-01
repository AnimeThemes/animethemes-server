<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Group;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Group;
use Illuminate\Http\Request;

class GroupSlugField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Group::ATTRIBUTE_SLUG);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
            'alpha_dash',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:192',
            'alpha_dash',
        ];
    }
}
