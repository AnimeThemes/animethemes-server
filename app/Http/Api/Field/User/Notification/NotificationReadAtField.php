<?php

declare(strict_types=1);

namespace App\Http\Api\Field\User\Notification;

use App\Http\Api\Field\DateField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\User\Notification;

class NotificationReadAtField extends DateField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Notification::ATTRIBUTE_READ_AT);
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }
}
