<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video\Script;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class ScriptIdField.
 */
class ScriptIdField extends IdField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, VideoScript::ATTRIBUTE_ID);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        $linkField = new ScriptLinkField($this->schema);
        if (
            $this->schema->type() === $query->schema()->type()
            && ($includeCriteria === null || $includeCriteria->getPaths()->isEmpty())
        ) {
            return parent::shouldSelect($query) || $linkField->shouldRender($query);
        }

        return true;
    }
}
