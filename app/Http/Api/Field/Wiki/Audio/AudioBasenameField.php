<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;

class AudioBasenameField extends StringField implements CreatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Audio::ATTRIBUTE_BASENAME);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $linkField = new AudioLinkField($this->schema);
        // The link field is dependent on this field to build the route.
        if (parent::shouldSelect($query, $schema)) {
            return true;
        }

        return $linkField->shouldRender($query);
    }
}
