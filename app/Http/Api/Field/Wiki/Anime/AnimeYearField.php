<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        return [
            'required',
            'integer',
            'digits:4',
            'min:1960',
            Str::of('max:')->append(intval(date('Y')) + 1)->__toString(),
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
        return [
            'sometimes',
            'required',
            'integer',
            'digits:4',
            'min:1960',
            Str::of('max:')->append(intval(date('Y')) + 1)->__toString(),
        ];
    }
}
