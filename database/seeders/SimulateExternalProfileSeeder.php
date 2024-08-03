<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Models\List\ExternalProfile\StoreExternalProfileAction;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Seeder;

/**
 * Class SimulateExternalProfileSeeder.
 */
class SimulateExternalProfileSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $action = new StoreExternalProfileAction();

        $validated = [
            'name' => 'MiltonXerox',
            'site' => 'AniList',
            'visibility' => 'Public',
            'user_id' => 1,
        ];

        $action->store(ExternalProfile::query(), $validated);
    }
}
