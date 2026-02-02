<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Admin;

use App\Http\Api\Field\Admin\Announcement\AnnouncementContentField;
use App\Http\Api\Field\Admin\Announcement\AnnouncementEndAtField;
use App\Http\Api\Field\Admin\Announcement\AnnouncementStartAtField;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Admin\Resource\AnnouncementJsonResource;
use App\Models\Admin\Announcement;

class AnnouncementSchema extends EloquentSchema
{
    public function type(): string
    {
        return AnnouncementJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([]);
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new IdField($this, Announcement::ATTRIBUTE_ID),
            new AnnouncementStartAtField($this),
            new AnnouncementEndAtField($this),
            new AnnouncementContentField($this),
        ];
    }
}
