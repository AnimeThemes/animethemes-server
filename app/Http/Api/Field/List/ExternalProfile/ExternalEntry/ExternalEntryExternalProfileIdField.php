<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile\ExternalEntry;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\External\ExternalEntry;

class ExternalEntryExternalProfileIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalEntry::ATTRIBUTE_PROFILE);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match profile relation.
        return true;
    }
}
