<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song\Membership;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Song\Membership;
use Illuminate\Http\Request;

class MembershipMemberIdField extends Field implements CreatableField, SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Membership::ATTRIBUTE_MEMBER);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match song relation.
        return true;
    }
}
