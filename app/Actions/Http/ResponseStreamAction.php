<?php

declare(strict_types=1);

namespace App\Actions\Http;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ResponseStreamAction.
 */
abstract class ResponseStreamAction extends StreamAction
{
    /**
     * Stream the resource.
     *
     * @param string $disposition
     * @return StreamedResponse
     */
    public function stream(string $disposition = 'inline'): StreamedResponse
    {
        $response = new StreamedResponse();

        $disposition = $response->headers->makeDisposition($disposition, $this->streamable->basename());

        $response->headers->replace([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $this->streamable->mimetype(),
            'Content-Length' => $this->streamable->size(),
            'Content-Disposition' => $disposition,
        ]);

        $fs = Storage::disk($this->disk());

        $response->setCallback(function () use ($fs) {
            $stream = $fs->readStream($this->streamable->path());
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
