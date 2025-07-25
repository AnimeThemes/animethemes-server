<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Pivots\Wiki\ArtistMember;

#[UseFieldDirective(PivotResolver::class)]
class ArtistMemberNotesField extends StringField
{
    public function __construct()
    {
        parent::__construct(ArtistMember::ATTRIBUTE_NOTES);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to extra annotation, like member role';
    }
}
