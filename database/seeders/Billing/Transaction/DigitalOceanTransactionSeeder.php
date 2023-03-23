<?php

declare(strict_types=1);

namespace Database\Seeders\Billing\Transaction;

use App\Actions\Repositories\Billing\Transaction\ReconcileTransactionRepositoriesAction;
use App\Repositories\DigitalOcean\Billing\DigitalOceanTransactionRepository as DigitalOceanSourceRepository;
use App\Repositories\Eloquent\Billing\DigitalOceanTransactionRepository as DigitalOceanDestinationRepository;
use Exception;
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
     *
     * @throws Exception
     */
    public function run(): void
    {
        $sourceRepository = App::make(DigitalOceanSourceRepository::class);

        $destinationRepository = App::make(DigitalOceanDestinationRepository::class);

        $action = new ReconcileTransactionRepositoriesAction();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();
        $results->toConsole($this->command);
    }
}
