<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song\Membership;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Song\Membership;
use Illuminate\Http\Request;

class MembershipAsField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Membership::ATTRIBUTE_AS);
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
