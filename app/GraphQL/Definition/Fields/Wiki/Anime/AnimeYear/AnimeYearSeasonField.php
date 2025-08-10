<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYear;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\HasArgumentsField;
use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Controllers\Wiki\Anime\AnimeYearsController;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Rebing\GraphQL\Support\Facades\GraphQL;

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
    public function baseType(): AnimeYearSeasonType
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
        $season = GraphQL::type(class_basename(AnimeSeason::class));

        return [
            new Argument(self::ARGUMENT_SEASON, $season)
                ->required(),
        ];
    }

    /**
     * @return Collection
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return App::make(AnimeYearsController::class)
            ->resolveSeasonField($root, $args, $context, $resolveInfo);
    }
}
