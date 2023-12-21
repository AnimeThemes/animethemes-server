<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_SLUG, 'OP')->update([
            AnimeTheme::ATTRIBUTE_SLUG => 'OP1'
        ]);
        
        AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_SLUG, 'ED')->update([
            AnimeTheme::ATTRIBUTE_SLUG => 'ED1'
        ]);

        AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_SLUG, 'LIKE', 'OP-%')->update([
            AnimeTheme::ATTRIBUTE_SLUG => DB::raw('CONCAT("OP1-", SUBSTRING(slug, 4))')
        ]);

        AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_SLUG, 'LIKE', 'ED-%')->update([
            AnimeTheme::ATTRIBUTE_SLUG => DB::raw('CONCAT("ED1-", SUBSTRING(slug, 4))')
        ]);
    }
}
