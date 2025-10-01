<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Synonym;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SynonymTypeField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::class);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(AnimeSynonymType::class),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(AnimeSynonymType::class),
        ];
    }
}
