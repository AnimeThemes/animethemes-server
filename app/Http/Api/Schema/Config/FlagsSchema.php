<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Config;

use App\Constants\Config\FlagConstants;
use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Field\BooleanField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Config\Resource\FlagsResource;

/**
 * Class FlagsSchema.
 */
class FlagsSchema extends Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return FlagsResource::$wrap;
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
            new BooleanField(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG, null, Category::COMPUTED()),
            new BooleanField(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG, null, Category::COMPUTED()),
            new BooleanField(FlagConstants::ALLOW_VIEW_RECORDING_FLAG, null, Category::COMPUTED()),
        ];
    }
}
