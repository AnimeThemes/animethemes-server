<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Admin\Announcement;
use App\Models\Admin\Dump;
use App\Models\Admin\Setting;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Billing\Balance;
use App\Models\Billing\Transaction;
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
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Admin');

        // Admin Resources
        $this->configureResource($role, Announcement::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Dump::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Setting::class, CrudPermission::getInstances());

        // Auth Resources
        $this->configureResource($role, Permission::class, [CrudPermission::VIEW()]);
        $this->configureResource($role, Role::class, CrudPermission::getInstances());
        $this->configureResource($role, User::class, ExtendedCrudPermission::getInstances());

        // Billing Resources
        $this->configureResource($role, Balance::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Transaction::class, ExtendedCrudPermission::getInstances());

        // List Resources
        $this->configureResource($role, Playlist::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, PlaylistTrack::class, ExtendedCrudPermission::getInstances());

        // Wiki Resources
        $this->configureResource($role, Anime::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, AnimeSynonym::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, AnimeTheme::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, AnimeThemeEntry::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Artist::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Audio::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, ExternalResource::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Image::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Page::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Series::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Song::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Studio::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, Video::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, VideoScript::class, ExtendedCrudPermission::getInstances());

        // Special Permissions
        $this->configureAbilities($role, SpecialPermission::getValues());
    }
}
