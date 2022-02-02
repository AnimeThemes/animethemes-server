<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Admin;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseResource;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementSchema.
 */
class AnnouncementSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Announcement::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnnouncementResource::$wrap;
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
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IntField(BaseResource::ATTRIBUTE_ID, Announcement::ATTRIBUTE_ID),
                new StringField(Announcement::ATTRIBUTE_CONTENT),
            ],
        );
    }
}
