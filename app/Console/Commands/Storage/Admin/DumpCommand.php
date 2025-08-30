<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction;
use App\Console\Commands\BaseCommand;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

abstract class DumpCommand extends BaseCommand
{
    /**
     * @throws Exception
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

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'default-character-set' => ['string'],
            'set-gtid-purged' => [Rule::in(['OFF', 'ON', 'AUTO'])->__toString()],
        ]);
    }

    abstract protected function action(): DumpAction;
}
