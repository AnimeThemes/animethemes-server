<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Permission;

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
        // Admin Resources
        $this->registerResource(Announcement::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Dump::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Setting::class, CrudPermission::getInstances());

        // Auth Resources
        $this->registerResource(Permission::class, [CrudPermission::VIEW()]);
        $this->registerResource(Role::class, CrudPermission::getInstances());
        $this->registerResource(User::class, ExtendedCrudPermission::getInstances());

        // Billing Resources
        $this->registerResource(Balance::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Transaction::class, ExtendedCrudPermission::getInstances());

        // List Resources
        $this->registerResource(Playlist::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(PlaylistTrack::class, ExtendedCrudPermission::getInstances());

        // Wiki Resources
        $this->registerResource(Anime::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(AnimeSynonym::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(AnimeTheme::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(AnimeThemeEntry::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Artist::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Audio::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(ExternalResource::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Image::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Page::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Series::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Song::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Studio::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(Video::class, ExtendedCrudPermission::getInstances());
        $this->registerResource(VideoScript::class, ExtendedCrudPermission::getInstances());

        // Special Permissions
        $this->registerAbilities(SpecialPermission::getValues());
    }

    /**
     * Register resource abilities.
     *
     * @param  string  $resource
     * @param  CrudPermission[]  $abilities
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
