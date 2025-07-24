<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Pivots\Wiki\ArtistMember;

#[UseField(PivotResolver::class)]
class ArtistMemberAsField extends StringField
{
    public function __construct()
    {
        parent::__construct(ArtistMember::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to distinguish member by character';
    }
}
