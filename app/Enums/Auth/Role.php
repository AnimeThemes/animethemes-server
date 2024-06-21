<?php

declare(strict_types=1);

namespace App\Enums\Auth;

/**
 * Enum Role.
 */
enum Role: string
{
    case ADMIN = 'Admin';

    case DEVELOPER = 'Developer';

    case ENCODER = 'Encoder';

    case PATRON = 'Patron';

    case PLAYLIST_USER = 'Playlist User';

    case WIKI_EDITOR = 'Wiki Editor';

    case WIKI_VIEWER = 'Wiki Viewer';

    /**
     * Get the color for the role.
     *
     * @return string|null
     */
    public function color(): ?string
    {
        return match ($this) {
            Role::ADMIN => '#1F8B4C',
            Role::DEVELOPER => '#FF69B4',
            Role::ENCODER => '#FFC107',
            Role::PATRON => '#E74C3C',
            Role::WIKI_EDITOR => '#2E5A88',
            Role::WIKI_VIEWER => '#2596D1',
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
            Role::DEVELOPER => 125000,
            Role::ENCODER => 150000,
            Role::PATRON => 50000,
            Role::WIKI_EDITOR => 100000,
            Role::WIKI_VIEWER => 25000,
            default => null,
        };
    }
}
