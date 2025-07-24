<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\Role;
use App\Models\Discord\DiscordThread;
use App\Models\Document\Page;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\User\Like;
use App\Models\User\Notification;
use App\Models\User\Report;
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
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;

class PanelViewerRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function run(): void
    {
        $roleEnum = RoleEnum::PANEL_VIEWER;

        /** @var Role $role */
        $role = Role::findOrCreate($roleEnum->value);

        $extendedCrudPermissions = array_merge(
            CrudPermission::cases(),
            ExtendedCrudPermission::cases(),
        );

        // Discord Resources
        $this->configureResource($role, DiscordThread::class, [CrudPermission::VIEW]);

        // List Resources
        $this->configureResource($role, ExternalEntry::class, [CrudPermission::VIEW]);
        $this->configureResource($role, ExternalProfile::class, CrudPermission::cases());
        $this->configureResource($role, Playlist::class, CrudPermission::cases());
        $this->configureResource($role, PlaylistTrack::class, CrudPermission::cases());

        // User Resources
        $this->configureResource($role, Like::class, [CrudPermission::VIEW, CrudPermission::CREATE, CrudPermission::DELETE]);
        $this->configureResource($role, Notification::class, [CrudPermission::VIEW, CrudPermission::UPDATE]);
        $this->configureResource($role, Report::class, [CrudPermission::VIEW, CrudPermission::CREATE, CrudPermission::UPDATE]);

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
        $this->configureResource($role, Membership::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Page::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Performance::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Series::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Song::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Studio::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Video::class, [CrudPermission::VIEW]);
        $this->configureResource($role, VideoScript::class, [CrudPermission::VIEW]);

        // Special Permissions
        $this->configureAbilities(
            $role,
            [
                SpecialPermission::VIEW_FILAMENT->value,
            ]
        );

        $role->color = $roleEnum->color();
        $role->priority = $roleEnum->priority();

        $role->save();
    }
}
