<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Synonym;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\SynonymType;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\Wiki\Synonym;
use Illuminate\Validation\Rules\Enum;

class SynonymTypeField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Synonym::ATTRIBUTE_TYPE, SynonymType::class, nullable: false);
    }

    public function description(): string
    {
        return 'The type of the synonym';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            new Enum(SynonymType::class),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            new Enum(SynonymType::class),
        ];
    }
}
