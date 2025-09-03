<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Admin;

use App\GraphQL\Schema\Fields\Admin\Dump\DumpLinkField;
use App\GraphQL\Schema\Fields\Admin\Dump\DumpPathField;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use App\Models\Admin\Dump;

class DumpType extends EloquentType
{
    public function description(): string
    {
        return "Represents a database dump of selected tables at a given point in time.\n\nFor example, the animethemes-db-dump-wiki-1663559663946.sql dump represents the database dump of wiki tables performed at 2022-09-19.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Dump::ATTRIBUTE_ID, Dump::class),
            new DumpPathField(),
            new DumpLinkField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
