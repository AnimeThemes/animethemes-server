<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Entry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\BooleanField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Entry;
use Illuminate\Http\Request;

class EntryNsfwField extends BooleanField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Entry::ATTRIBUTE_NSFW);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
        ];
    }
}
