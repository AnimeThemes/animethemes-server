<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Class DatabaseDumpCommand.
 */
abstract class DumpCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $validator = $this->validator();

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Log::error($error);
                $this->error($error);
            }

            return 1;
        }

        $action = $this->action();

        $result = $action->handle();

        $result->toLog();
        $result->toConsole($this);

        return $result->hasFailed() ? 1 : 0;
    }

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'default-character-set' => ['string'],
            'set-gtid-purged' => [Rule::in(['OFF', 'ON', 'AUTO'])->__toString()],
        ]);
    }

    /**
     * Get the underlying action.
     *
     * @return DumpAction
     */
    abstract protected function action(): DumpAction;
}
