<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\FeaturedTheme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeaturedThemeUserIdField extends Field implements CreatableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, FeaturedTheme::ATTRIBUTE_USER);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(User::class, User::ATTRIBUTE_ID),
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match user relation.
        return true;
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(User::class, User::ATTRIBUTE_ID),
        ];
    }
}
