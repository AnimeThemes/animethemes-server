<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\Role;
use App\Models\Document\Page;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Audio;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class PatronRoleSeeder.
 */
class PatronRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Patron');

        $extendedCrudPermissions = array_merge(
            CrudPermission::cases(),
            ExtendedCrudPermission::cases(),
        );

        // List Resources
        $this->configureResource($role, Playlist::class, $extendedCrudPermissions);
        $this->configureResource($role, PlaylistTrack::class, $extendedCrudPermissions);

        // Wiki Resources
        $this->configureResource($role, Anime::class, [CrudPermission::VIEW]);
        $this->configureResource($role, AnimeSynonym::class, [CrudPermission::VIEW]);
        $this->configureResource($role, AnimeTheme::class, [CrudPermission::VIEW]);
        $this->configureResource($role, AnimeThemeEntry::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Artist::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Audio::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Group::class, [CrudPermission::VIEW]);
        $this->configureResource($role, ExternalResource::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Image::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Page::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Series::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Song::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Studio::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Video::class, [CrudPermission::VIEW]);
        $this->configureResource($role, VideoScript::class, [CrudPermission::VIEW]);

        // Special Permissions
        $this->configureAbilities(
            $role,
            [
                SpecialPermission::BYPASS_FEATURE_FLAGS->value,
                SpecialPermission::VIEW_NOVA->value,
                SpecialPermission::VIEW_FILAMENT->value,
            ]
        );

        $role->color = '#E74C3C';
        $role->priority = 50000;

        $role->save();
    }
}
