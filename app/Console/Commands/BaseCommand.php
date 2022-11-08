<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class BaseCommand.
 */
abstract class BaseCommand extends Command implements Isolatable
{
    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    abstract public function handle(): int;

    /**
     * Get the validator for options.
     *
     * @return Validator
     */
    abstract protected function validator(): Validator;

    /**
     * Configure the console command for isolation.
     * Note: Overrides default framework behavior which disables isolation by default.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function configureIsolation(): void
    {
        $this->getDefinition()->addOption(new InputOption(
            'isolated',
            null,
            InputOption::VALUE_OPTIONAL,
            'Do not run the command if another instance of the command is already running',
            true
        ));
    }
}
