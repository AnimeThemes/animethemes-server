<?php

declare(strict_types=1);

namespace Database\Seeder\Wiki;

use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class KitsuResourceSeeder.
 */
class KitsuResourceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_LINK, 'like', 'https://kitsu.io/%')
            ->update([
                ExternalResource::ATTRIBUTE_LINK => DB::raw("REPLACE(link, 'https://kitsu.io/', 'https://kitsu.app/')")
            ]);
    }
}
