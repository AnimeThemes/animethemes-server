<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistMember;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Http\Request;

class ArtistMemberDetailsField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistMember::ATTRIBUTE_NOTES);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }

    /**
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }
}
