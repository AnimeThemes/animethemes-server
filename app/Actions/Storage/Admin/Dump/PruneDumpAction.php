<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Actions\Storage\Base\PruneAction;
use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Constants\Config\DumpConstants;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class PruneDumpAction extends PruneAction
{
    use ReconcilesDumpRepositories;

    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }

    protected function shouldBePruned(string $path, Carbon $lastModified): bool
    {
        if (Str::contains($path, ['content', 'wiki'])) {
            return true;
        }

        return $lastModified->isBefore(Date::now()->subWeeks(3));
    }
}
