<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile\ExternalEntry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\FloatField;
use App\Http\Api\Schema\Schema;
use App\Models\List\External\ExternalEntry;
use Illuminate\Http\Request;

class ExternalEntryScoreField extends FloatField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalEntry::ATTRIBUTE_SCORE);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'decimal:0,2',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'decimal:0,2',
        ];
    }
}
