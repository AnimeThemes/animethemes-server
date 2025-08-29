<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;

class AnimeYearField extends IntField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_YEAR);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        $nextYear = intval(date('Y')) + 1;

        return [
            'required',
            'integer',
            'digits:4',
            'min:1960',
            "max:$nextYear",
        ];
    }

    /**
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        $nextYear = intval(date('Y')) + 1;

        return [
            'sometimes',
            'required',
            'integer',
            'digits:4',
            'min:1960',
            "max:$nextYear",
        ];
    }
}
