<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Group;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Seeder;

/**
 * Class GroupMigratingSeeder.
 */
class GroupMigratingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->englishVersionGroup();
        $this->tvVersionGroup();
        $this->bdVersionGroup();
        $this->koreanVersionGroup();
        $this->hdRemasterGroup();
        $this->gintamaYorinukiGroup();
    }

    protected function englishVersionGroup()
    {
        $dubbedGroupThemes = AnimeTheme::query()
            ->where(AnimeTheme::ATTRIBUTE_GROUP, 'Dubbed Version')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, 'Dubbed Version - Funimation')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, 'English Version')
            ->get();

        $dubbedGroup = Group::query()->where(Group::ATTRIBUTE_NAME, 'English Version')->get();
        $dubbedGroupThemes->each(fn (AnimeTheme $theme) => $theme->theme_group()->associate($dubbedGroup)->save());
    }

    protected function tvVersionGroup()
    {
        $tvVersionThemes = AnimeTheme::query()
            ->where(AnimeTheme::ATTRIBUTE_GROUP, 'Original Broadcast Version')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, 'Original Japanese Version')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, 'Japanese Terrestrial Broadcast')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, '2005 Japanese Terrestrial Broadcast')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, 'TV version')
            ->orWhere(AnimeTheme::ATTRIBUTE_GROUP, 'TV Broadcast')
            ->get();

        $tvVersionGroup = Group::query()->where(Group::ATTRIBUTE_NAME, 'TV Version')->get();
        $tvVersionThemes->each(fn (AnimeTheme $theme) => $theme->theme_group()->associate($tvVersionGroup)->save());

    }

    protected function bdVersionGroup()
    {
        $bdVersionThemes = AnimeTheme::query()
            ->where(AnimeTheme::ATTRIBUTE_GROUP, 'BD version')
            ->get();

        $bdVersionGroup = Group::query()->where(Group::ATTRIBUTE_NAME, 'BD Version')->get();
        $bdVersionThemes->each(fn (AnimeTheme $theme) => $theme->theme_group()->associate($bdVersionGroup)->save());
    }

    protected function koreanVersionGroup()
    {
        $koreanVersionThemes = AnimeTheme::query()
            ->where(AnimeTheme::ATTRIBUTE_GROUP, 'Korean Version')
            ->get();

        $koreanVersionGroup = Group::query()->where(Group::ATTRIBUTE_NAME, 'Korean Version')->get();
        $koreanVersionThemes->each(fn (AnimeTheme $theme) => $theme->theme_group()->associate($koreanVersionGroup)->save());
    }

    protected function hdRemasterGroup()
    {
        $hdRemasterThemes = AnimeTheme::query()
            ->where(AnimeTheme::ATTRIBUTE_GROUP, 'HD Remaster')
            ->get();

        $hdRemasterGroup = Group::query()->where(Group::ATTRIBUTE_NAME, 'HD Remaster')->get();
        $hdRemasterThemes->each(fn (AnimeTheme $theme) => $theme->theme_group()->associate($hdRemasterGroup)->save());
    }

    protected function gintamaYorinukiGroup()
    {
        $gintamaYorinukiThemes = AnimeTheme::query()
            ->where(AnimeTheme::ATTRIBUTE_GROUP, 'Yorinuki Gintama-san')
            ->get();

        $gintamaYorinukiGroup = Group::query()->where(Group::ATTRIBUTE_NAME, 'Yorinuki Gintama-san')->get();
        $gintamaYorinukiThemes->each(fn (AnimeTheme $theme) => $theme->theme_group()->associate($gintamaYorinukiGroup)->save());
    }
}
