<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Admin\Dump\DumpLinkField;
use App\GraphQL\Definition\Fields\Admin\Dump\DumpPathField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Admin\Dump;

/**
 * Class DumpType.
 */
class DumpType extends EloquentType implements HasFields
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a database dump of selected tables at a given point in time.\n\nFor example, the animethemes-db-dump-wiki-1663559663946.sql dump represents the database dump of wiki tables performed at 2022-09-19.";
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new IdField(Dump::ATTRIBUTE_ID),
            new DumpPathField(),
            new DumpLinkField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
