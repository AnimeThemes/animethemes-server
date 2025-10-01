<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video\Script;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video\VideoScript;

class ScriptIdField extends IdField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, VideoScript::ATTRIBUTE_ID);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        $linkField = new ScriptLinkField($this->schema);
        if ($this->schema->type() === $schema->type()
        && (! $includeCriteria instanceof Criteria || $includeCriteria->getPaths()->isEmpty())) {
            if (parent::shouldSelect($query, $schema)) {
                return true;
            }

            return $linkField->shouldRender($query);
        }

        return true;
    }
}
