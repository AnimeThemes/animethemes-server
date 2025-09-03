<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Document;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Document\Page\PageBodyField;
use App\GraphQL\Schema\Fields\Document\Page\PageNameField;
use App\GraphQL\Schema\Fields\Document\Page\PageSlugField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use App\Models\Document\Page;

class PageType extends EloquentType implements ReportableType
{
    public function description(): string
    {
        return "Represents a static markdown page used for guides and other documentation.\n\nFor example, the 'encoding/audio_normalization' page represents the documentation for audio normalization.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdUnbindableField(Page::ATTRIBUTE_ID),
            new PageNameField(),
            new PageSlugField(),
            new PageBodyField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
