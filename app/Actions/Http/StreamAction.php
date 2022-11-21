<?php

declare(strict_types=1);

namespace App\Actions\Http;

use App\Contracts\Models\Streamable;
use App\Contracts\Storage\InteractsWithDisk;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StreamAction.
 */
abstract class StreamAction implements InteractsWithDisk
{
    /**
     * Create a new action instance.
     *
     * @param  Streamable  $streamable
     */
    public function __construct(protected readonly Streamable $streamable)
    {
    }

    /**
     * Stream the resource.
     *
     * @return Response
     */
    abstract public function stream(): Response;
}
