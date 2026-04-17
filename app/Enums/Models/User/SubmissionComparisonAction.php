<?php

declare(strict_types=1);

namespace App\Enums\Models\User;

enum SubmissionComparisonAction: int
{
    case CREATE = 0;
    case ATTACH = 1;
    case UPDATE = 2;

    public static function getByBlockType(string $type): static
    {
        return match ($type) {
            'create' => self::CREATE,
            'update' => self::UPDATE,
            default => self::ATTACH,
        };
    }
}
