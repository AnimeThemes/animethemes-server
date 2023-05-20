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
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;

/**
 * Class WikiEditorRoleSeeder.
 */
class WikiEditorRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Wiki Editor');

        $extendedCrudPermissions = ExtendedCrudPermission::getInstances();

        // List Resources
        $this->configureResource($role, Playlist::class, $extendedCrudPermissions);
        $this->configureResource($role, PlaylistTrack::class, $extendedCrudPermissions);

        Arr::forget($extendedCrudPermissions, ExtendedCrudPermission::FORCE_DELETE()->key);

        // Wiki Resources
        $this->configureResource($role, Anime::class, $extendedCrudPermissions);
        $this->configureResource($role, AnimeSynonym::class, $extendedCrudPermissions);
        $this->configureResource($role, AnimeTheme::class, $extendedCrudPermissions);
        $this->configureResource($role, AnimeThemeEntry::class, $extendedCrudPermissions);
        $this->configureResource($role, Artist::class, $extendedCrudPermissions);
        $this->configureResource($role, Audio::class, [CrudPermission::VIEW(), CrudPermission::UPDATE()]);
        $this->configureResource($role, ExternalResource::class, $extendedCrudPermissions);
        $this->configureResource($role, Image::class, $extendedCrudPermissions);
        $this->configureResource($role, Page::class, $extendedCrudPermissions);
        $this->configureResource($role, Series::class, $extendedCrudPermissions);
        $this->configureResource($role, Song::class, $extendedCrudPermissions);
        $this->configureResource($role, Studio::class, $extendedCrudPermissions);
        $this->configureResource($role, Video::class, [CrudPermission::VIEW(), CrudPermission::UPDATE()]);
        $this->configureResource($role, VideoScript::class, [CrudPermission::VIEW()]);

        // Special Permissions
        $this->configureAbilities($role, [SpecialPermission::VIEW_NOVA]);

        $role->color = '#2E5A88';
        $role->priority = 100000;

        $role->save();
    }
}
