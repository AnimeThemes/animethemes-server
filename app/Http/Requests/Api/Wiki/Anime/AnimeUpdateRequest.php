<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Enums\BaseEnum;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\AnimeQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\Base\EloquentUpdateRequest;
use App\Models\Wiki\Anime;

/**
 * Class AnimeUpdateRequest.
 */
class AnimeUpdateRequest extends EloquentUpdateRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new AnimeQuery();
    }

    /**
     * The list of enum attributes to convert.
     *
     * @return array<string, class-string<BaseEnum>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function enums(): array
    {
        return [
            Anime::ATTRIBUTE_SEASON => AnimeSeason::class,
        ];
    }
}
