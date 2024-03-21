<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Admin\Announcement;
use App\Models\Admin\Dump;
use App\Models\Admin\Feature;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Document\Page;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Audio;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class AdminSeeder.
 */
class AdminSeeder extends RoleSeeder
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
        $role = Role::findOrCreate('Admin');

        $extendedCrudPermissions = array_merge(
            CrudPermission::cases(),
            ExtendedCrudPermission::cases(),
        );

        // Admin Resources
        $this->configureResource($role, Announcement::class, $extendedCrudPermissions);
        $this->configureResource($role, Dump::class, $extendedCrudPermissions);
        $this->configureResource($role, Feature::class, [CrudPermission::VIEW, CrudPermission::UPDATE]);
        $this->configureResource($role, FeaturedTheme::class, $extendedCrudPermissions);

        // Auth Resources
        $this->configureResource($role, Permission::class, [CrudPermission::VIEW]);
        $this->configureResource($role, Role::class, CrudPermission::cases());
        $this->configureResource($role, User::class, $extendedCrudPermissions);

        // List Resources
        $this->configureResource($role, ExternalEntry::class, $extendedCrudPermissions);
        $this->configureResource($role, ExternalProfile::class, $extendedCrudPermissions);
        $this->configureResource($role, Playlist::class, $extendedCrudPermissions);
        $this->configureResource($role, PlaylistTrack::class, $extendedCrudPermissions);

        // Wiki Resources
        $this->configureResource($role, Anime::class, $extendedCrudPermissions);
        $this->configureResource($role, AnimeSynonym::class, $extendedCrudPermissions);
        $this->configureResource($role, AnimeTheme::class, $extendedCrudPermissions);
        $this->configureResource($role, AnimeThemeEntry::class, $extendedCrudPermissions);
        $this->configureResource($role, Artist::class, $extendedCrudPermissions);
        $this->configureResource($role, Audio::class, $extendedCrudPermissions);
        $this->configureResource($role, ExternalResource::class, $extendedCrudPermissions);
        $this->configureResource($role, Image::class, $extendedCrudPermissions);
        $this->configureResource($role, Page::class, $extendedCrudPermissions);
        $this->configureResource($role, Series::class, $extendedCrudPermissions);
        $this->configureResource($role, Song::class, $extendedCrudPermissions);
        $this->configureResource($role, Studio::class, $extendedCrudPermissions);
        $this->configureResource($role, Video::class, $extendedCrudPermissions);
        $this->configureResource($role, VideoScript::class, $extendedCrudPermissions);

        // Special Permissions
        $this->configureAbilities($role, array_column(SpecialPermission::cases(), 'value'));

        $role->color = '#1F8B4C';
        $role->priority = 250000;

        $role->save();
    }
}
