<?php

declare(strict_types=1);

namespace App\Actions\Http;

use App\Contracts\Models\Streamable;
use App\Contracts\Storage\InteractsWithDisk;
use Symfony\Component\HttpFoundation\Response;

abstract class StreamAction implements InteractsWithDisk
{
    public function __construct(protected readonly Streamable $streamable) {}

    abstract public function stream(string $disposition = 'inline'): Response;
}
