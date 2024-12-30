<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Permission;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Admin\Announcement;
use App\Models\Admin\Dump;
use App\Models\Admin\Feature;
use App\Models\Admin\FeaturedTheme;
use App\Models\Admin\Report;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Discord\DiscordThread;
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
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Seeder;

/**
 * Class PermissionSeeder.
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $extendedCrudPermissions = array_merge(
            CrudPermission::cases(),
            ExtendedCrudPermission::cases(),
        );

        // Admin Resources
        $this->registerResource(Announcement::class, $extendedCrudPermissions);
        $this->registerResource(Dump::class, $extendedCrudPermissions);
        $this->registerResource(Feature::class, CrudPermission::cases());
        $this->registerResource(FeaturedTheme::class, $extendedCrudPermissions);
        $this->registerResource(Report::class, CrudPermission::cases());

        // Auth Resources
        $this->registerResource(Permission::class, [CrudPermission::VIEW]);
        $this->registerResource(Role::class, CrudPermission::cases());
        $this->registerResource(User::class, $extendedCrudPermissions);

        // Discord Resources
        $this->registerResource(DiscordThread::class, CrudPermission::cases());

        // List Resources
        $this->registerResource(ExternalEntry::class, [CrudPermission::VIEW]);
        $this->registerResource(ExternalProfile::class, $extendedCrudPermissions);
        $this->registerResource(Playlist::class, $extendedCrudPermissions);
        $this->registerResource(PlaylistTrack::class, $extendedCrudPermissions);

        // Wiki Resources
        $this->registerResource(Anime::class, $extendedCrudPermissions);
        $this->registerResource(AnimeSynonym::class, $extendedCrudPermissions);
        $this->registerResource(AnimeTheme::class, $extendedCrudPermissions);
        $this->registerResource(AnimeThemeEntry::class, $extendedCrudPermissions);
        $this->registerResource(Artist::class, $extendedCrudPermissions);
        $this->registerResource(Audio::class, $extendedCrudPermissions);
        $this->registerResource(Group::class, $extendedCrudPermissions);
        $this->registerResource(ExternalResource::class, $extendedCrudPermissions);
        $this->registerResource(Image::class, $extendedCrudPermissions);
        $this->registerResource(Page::class, $extendedCrudPermissions);
        $this->registerResource(Series::class, $extendedCrudPermissions);
        $this->registerResource(Song::class, $extendedCrudPermissions);
        $this->registerResource(Studio::class, $extendedCrudPermissions);
        $this->registerResource(Video::class, $extendedCrudPermissions);
        $this->registerResource(VideoScript::class, $extendedCrudPermissions);

        // Special Permissions
        $this->registerAbilities(array_column(SpecialPermission::cases(), 'value'));
    }

    /**
     * Register resource abilities.
     *
     * @param  string  $resource
     * @param  array  $abilities
     * @return void
     */
    protected function registerResource(string $resource, array $abilities): void
    {
        foreach ($abilities as $ability) {
            Permission::findOrCreate($ability->format($resource));
        }
    }

    /**
     * Configure role with abilities.
     *
     * @param  array<int, string>  $abilities
     * @return void
     */
    protected function registerAbilities(array $abilities): void
    {
        foreach ($abilities as $ability) {
            Permission::findOrCreate($ability);
        }
    }
}
