<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class BaseCommand.
 */
abstract class BaseCommand extends Command implements Isolatable
{
    /**
     * Indicates whether only one instance of the command can run at any given time.
     *
     * @var bool
     */
    protected $isolated = true;

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
}
