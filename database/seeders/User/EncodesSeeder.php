<?php

declare(strict_types=1);

namespace Database\Seeders\User;

use App\Models\Admin\ActionLog;
use App\Models\User\Encode;
use App\Models\Wiki\Video;
use Illuminate\Database\Seeder;

/**
 * Class EncodesSeeder.
 */
class EncodesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        ActionLog::query()
            ->where(ActionLog::ATTRIBUTE_NAME, 'Upload Video')
            ->where(ActionLog::ATTRIBUTE_TARGET_TYPE, Video::class)
            ->latest()
            ->chunk(10, function (ActionLog $log) {
                Encode::query()
                    ->firstOrCreate([
                        Encode::ATTRIBUTE_VIDEO => $log->target_id,
                    ], [
                        Encode::ATTRIBUTE_USER => $log->user_id,
                    ]);
            });
    }
}
