<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song\Performance;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Song\Performance;
use Illuminate\Http\Request;

class PerformanceRelevanceField extends IntField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Performance::ATTRIBUTE_RELEVANCE);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'integer',
            'min:1',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:1',
        ];
    }
}
