<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video\Script;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Model;

class ScriptLinkField extends Field implements RenderableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, VideoScript::ATTRIBUTE_LINK);
    }

    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return ! $criteria instanceof Criteria || $criteria->isAllowedField($this->getKey());
    }

    public function render(Model $model): string
    {
        return $model->getAttribute($this->getColumn());
    }
}
