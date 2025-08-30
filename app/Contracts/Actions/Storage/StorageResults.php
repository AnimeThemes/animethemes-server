<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Storage;

use App\Actions\ActionResult;
use Illuminate\Console\Command;

interface StorageResults
{
    public function toLog(): void;

    public function toConsole(Command $command): void;

    public function toActionResult(): ActionResult;
}
