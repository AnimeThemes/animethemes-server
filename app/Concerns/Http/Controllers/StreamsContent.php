<?php

declare(strict_types=1);

namespace App\Concerns\Http\Controllers;

use App\Contracts\Streamable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Trait StreamsContent.
 */
trait StreamsContent
{
    /**
     * Stream content that the model represents.
     *
     * @param Streamable $streamable
     * @return StreamedResponse
     */
    public function streamContent(Streamable $streamable): StreamedResponse
    {
        $response = new StreamedResponse();

        $disposition = $response->headers->makeDisposition('inline', basename($streamable->getPath()));

        $response->headers->replace([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $streamable->getMimetype(),
            'Content-Length' => $streamable->getSize(),
            'Content-Disposition' => $disposition,
        ]);

        $fs = Storage::disk($streamable->getDisk());

        $response->setCallback(function () use ($fs, $streamable) {
            $stream = $fs->readStream($streamable->getPath());
            if ($stream !== null) {
                fpassthru($stream);
                fclose($stream);
            }
        });

        return $response;
    }
}
