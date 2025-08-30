<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistMember;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberMemberIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistMember::ATTRIBUTE_MEMBER);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match member relation.
        return true;
    }
}
