<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Synonym;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\SynonymType;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Synonym;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SynonymTypeField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Synonym::ATTRIBUTE_TYPE, SynonymType::class);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(SynonymType::class),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(SynonymType::class),
        ];
    }
}
