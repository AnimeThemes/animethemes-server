<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme\Entry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\BooleanField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;

/**
 * Class EntryNsfwField.
 */
class EntryNsfwField extends BooleanField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NSFW);
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
            'sometimes',
            'required',
            'boolean',
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
            'boolean',
        ];
    }
}
