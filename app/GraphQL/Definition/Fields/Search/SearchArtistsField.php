<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use GraphQL\Type\Definition\Type;

/**
 * Class SearchArtistsField.
 */
class SearchArtistsField extends Field implements DisplayableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct('artists', nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The artist results of the search';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    public function type(): Type
    {
        return Type::listOf(Type::nonNull(new ArtistType()));
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @return bool
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
