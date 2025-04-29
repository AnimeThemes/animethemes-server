<?php

declare(strict_types=1);

namespace App\Enums\Auth;

/**
 * Enum Role.
 */
enum Role: string
{
    case ADMIN = 'Admin';

    case CONTENT_MODERATOR = 'Content Moderator';

    case CONTRIBUTOR = 'Contributor';

    case DEVELOPER = 'Developer';

    case ENCODER = 'Encoder';

    case PANEL_VIEWER = 'Panel Viewer';

    case PATRON = 'Patron';

    case VERIFIED = 'Verified';

    /**
     * Get the color for the role.
     *
     * @return string|null
     */
    public function color(): ?string
    {
        return match ($this) {
            Role::ADMIN => '#1F8B4C',
            Role::CONTENT_MODERATOR => '#2E5A88',
            Role::CONTRIBUTOR => '#052C41',
            Role::DEVELOPER => '#FF69B4',
            Role::ENCODER => '#FFC107',
            Role::PANEL_VIEWER => '#2596D1',
            Role::PATRON => '#E74C3C',
            default => null,
        };
    }

    /**
     * Get the priority value for the role.
     *
     * @return int|null
     */
    public function priority(): ?int
    {
        return match ($this) {
            Role::ADMIN => 250000,
            Role::ENCODER => 150000,
            Role::DEVELOPER => 125000,
            Role::CONTENT_MODERATOR => 100000,
            Role::PATRON => 75000,
            Role::CONTRIBUTOR => 50000,
            Role::PANEL_VIEWER => 25000,
            default => null,
        };
    }
}
