<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile\ExternalEntry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\BooleanField;
use App\Http\Api\Schema\Schema;
use App\Models\List\External\ExternalEntry;
use Illuminate\Http\Request;

class ExternalEntryIsFavoriteField extends BooleanField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalEntry::ATTRIBUTE_IS_FAVORITE);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
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
            'boolean',
        ];
    }
}
