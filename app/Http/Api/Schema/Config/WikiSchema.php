<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Config;

use App\Http\Api\Field\Config\Wiki\WikiFeaturedThemeField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Config\Resource\WikiResource;

/**
 * Class WikiSchema.
 */
class WikiSchema extends Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return WikiResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new WikiFeaturedThemeField(),
        ];
    }
}
