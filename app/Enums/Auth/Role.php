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
            self::ADMIN => '#1F8B4C',
            self::DEVELOPER => '#FF69B4',
            self::ENCODER => '#FFC107',
            self::PATRON => '#E74C3C',
            self::PLAYLIST_USER => null,
            self::WIKI_EDITOR => '#2E5A88',
            self::WIKI_VIEWER => '#2596D1',
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
            self::ADMIN => 250000,
            self::DEVELOPER => 125000,
            self::ENCODER => 150000,
            self::PATRON => 50000,
            self::PLAYLIST_USER => null,
            self::WIKI_EDITOR => 100000,
            self::WIKI_VIEWER => 25000,
            default => null,
        };
    }
}
