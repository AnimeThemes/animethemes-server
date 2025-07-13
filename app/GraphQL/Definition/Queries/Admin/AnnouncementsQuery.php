<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Builders\Admin\AnnouncementBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\AnnouncementType;

/**
 * Class AnnouncementsQuery.
 */
class AnnouncementsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('announcements');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of announcement resources given fields.';
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'builder' => [
                'method' => AnnouncementBuilder::class.'@index',
            ],

            ...parent::directives(),
        ];
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "AnnouncementColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return AnnouncementType
     */
    public function baseType(): AnnouncementType
    {
        return new AnnouncementType();
    }
}
