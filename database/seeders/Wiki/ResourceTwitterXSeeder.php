<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki;

use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class ResourceTwitterXSeeder.
 */
class ResourceTwitterXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run(): void
    {
        ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_LINK, 'like', 'https://twitter.com/%')
            ->update([
                ExternalResource::ATTRIBUTE_LINK => DB::raw("REPLACE(link, 'https://twitter.com/', 'https://x.com/')")
            ]);
    }
}
