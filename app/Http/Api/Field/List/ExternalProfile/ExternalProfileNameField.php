<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\List\ExternalProfile;
use App\Rules\ModerationRule;
use Illuminate\Http\Request;

class ExternalProfileNameField extends StringField implements CreatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalProfile::ATTRIBUTE_NAME);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
            new ModerationRule(),
        ];
    }
}
