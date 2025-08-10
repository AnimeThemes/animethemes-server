<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Document;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Document\Page\PageBodyField;
use App\GraphQL\Definition\Fields\Document\Page\PageNameField;
use App\GraphQL\Definition\Fields\Document\Page\PageSlugField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Document\Page;

class PageType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
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
            new IdField(Page::ATTRIBUTE_ID, Page::class),
            new PageNameField(),
            new PageSlugField(),
            new PageBodyField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
