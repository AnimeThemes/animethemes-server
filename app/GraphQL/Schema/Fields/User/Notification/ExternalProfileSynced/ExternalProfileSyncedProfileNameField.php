<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\User\Notification\ExternalProfileSynced;

use App\GraphQL\Schema\Fields\DateTimeTzField;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;

class ExternalProfileSyncedProfileNameField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct('profileName', nullable: false);
    }

    public function description(): string
    {
        return 'The name of the profile';
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, 'data.profileName');
    }
}
