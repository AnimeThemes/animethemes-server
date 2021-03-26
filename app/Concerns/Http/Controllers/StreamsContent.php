<?php

namespace App\Concerns\Http\Controllers;

use App\Contracts\Streamable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait StreamsContent
{
    /**
     * Stream content that the model represents.
     *
     * @param Streamable $streamable
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamContent(Streamable $streamable)
    {
        $response = new StreamedResponse;

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
            if (! is_null($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        });

        return $response;
    }
}
