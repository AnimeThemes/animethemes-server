<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;

/**
 * Class AnimeYearField.
 */
class AnimeYearField extends IntField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_YEAR);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
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
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
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
