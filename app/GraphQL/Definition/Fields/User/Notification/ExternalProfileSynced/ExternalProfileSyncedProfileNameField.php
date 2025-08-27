<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\User\Notification\ExternalProfileSynced;

use App\GraphQL\Definition\Fields\DateTimeTzField;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;

class ExternalProfileSyncedProfileNameField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct('profileName', nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The name of the profile';
    }

    /**
     * Resolve the field.
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, 'data.profileName');
    }
}
