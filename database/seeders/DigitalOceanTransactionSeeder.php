<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Repositories\Billing\Transaction\ReconcileTransactionRepositories;
use App\Repositories\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

/**
 * Class DigitalOceanTransactionSeeder.
 */
class DigitalOceanTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $sourceRepository = App::make(DigitalOceanSourceRepository::class);

        $destinationRepository = App::make(DigitalOceanDestinationRepository::class);

        $action = new ReconcileTransactionRepositories();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this->command);
    }
}
