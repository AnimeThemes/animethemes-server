<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonsType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AnimeYearSeasonsField extends Field implements DisplayableField
{
    final public const FIELD = 'seasons';

    public function __construct()
    {
        parent::__construct(self::FIELD);
    }

    public function description(): string
    {
        return 'The available seasons of the year';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        $type = GraphQL::type(new AnimeYearSeasonsType()->getName());

        return Type::listOf(Type::nonNull($type));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
