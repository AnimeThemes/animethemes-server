<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Artist;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Artist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArtistSlugField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Artist::ATTRIBUTE_SLUG);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'max:192',
            'alpha_dash',
            Rule::unique(Artist::class),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'max:192',
            'alpha_dash',
            Rule::unique(Artist::class)->ignore($request->route('artist'), Artist::ATTRIBUTE_ID),
        ];
    }
}
