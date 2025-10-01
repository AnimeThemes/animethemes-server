<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Admin\Announcement;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Admin\Announcement;

class AnnouncementContentField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Announcement::ATTRIBUTE_CONTENT, nullable: false);
    }

    public function description(): string
    {
        return 'The announcement text';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'string',
            'max:65535',
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
            'string',
            'max:65535',
        ];
    }
}
