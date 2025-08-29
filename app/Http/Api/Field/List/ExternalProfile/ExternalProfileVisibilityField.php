<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\List\ExternalProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ExternalProfileVisibilityField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::class);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(ExternalProfileVisibility::class),
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
            new Enum(ExternalProfileVisibility::class),
        ];
    }
}
