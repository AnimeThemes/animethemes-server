<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Song\Performance;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\IntField;
use App\Models\Wiki\Song\Performance;

class PerformanceRelevanceField extends IntField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Performance::ATTRIBUTE_RELEVANCE);
    }

    public function description(): string
    {
        return 'Used to determine the relevance order of artists in performances';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'integer',
            'min:1',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'nullable',
            'integer',
            'min:1',
        ];
    }
}
