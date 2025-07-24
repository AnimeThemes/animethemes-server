<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Enums\Models\List\ExternalProfileSite;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\List\ExternalProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ExternalProfileSiteField extends EnumField implements CreatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalProfile::ATTRIBUTE_SITE, ExternalProfileSite::class);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(ExternalProfileSite::class),
        ];
    }
}
