<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\HasArgumentsField;
use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

#[UseFieldDirective(AnimeYearsController::class, 'applyFieldToSeasonField')]
class AnimeYearSeasonField extends Field implements DisplayableField, HasArgumentsField
{
    final public const FIELD = 'season';
    final public const ARGUMENT_SEASON = 'season';

    public function __construct()
    {
        parent::__construct(self::FIELD);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Object that references the season year queried';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return new AnimeYearSeasonType();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $season = app(TypeRegistry::class)->get(class_basename(AnimeSeason::class));

        return [
            new Argument(self::ARGUMENT_SEASON, Type::nonNull($season)),
        ];
    }
}
