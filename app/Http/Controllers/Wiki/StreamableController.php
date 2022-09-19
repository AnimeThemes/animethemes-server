<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki;

use App\Contracts\Models\Streamable;
use App\Contracts\Storage\InteractsWithDisk;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class StreamableController.
 */
abstract class StreamableController extends Controller implements InteractsWithDisk
{
    /**
     * Stream model through optimized framework streamed response.
     *
     * @param  Streamable  $streamable
     * @return StreamedResponse
     */
    protected function throughResponse(Streamable $streamable): StreamedResponse
    {
        $response = new StreamedResponse();

        $disposition = $response->headers->makeDisposition('inline', $streamable->basename());

        $response->headers->replace([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $streamable->mimetype(),
            'Content-Length' => $streamable->size(),
            'Content-Disposition' => $disposition,
        ]);

        $fs = Storage::disk($this->disk());

        $response->setCallback(function () use ($fs, $streamable) {
            $stream = $fs->readStream($streamable->path());
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * Stream model through configured nginx internal redirect.
     *
     * @param  Streamable  $streamable
     * @return Response
     */
    protected function throughNginx(Streamable $streamable): Response
    {
        $fs = Storage::disk($this->disk());

        // Generate temporary link for the object
        $temporaryURL = $fs->temporaryUrl($streamable->path(), now()->addMinutes(5));

        // Get the url information
        $url_scheme = parse_url($temporaryURL, PHP_URL_SCHEME);
        $url_host = parse_url($temporaryURL, PHP_URL_HOST);
        $url_path_query = parse_url($temporaryURL, PHP_URL_PATH).'?'.parse_url($temporaryURL, PHP_URL_QUERY);

        // Construct the new link for the redirect
        $link = "{$this->nginxRedirect()}$url_scheme/$url_host$url_path_query";

        $response = new Response();

        $disposition = $response->headers->makeDisposition('inline', $streamable->basename());

        return $response->withHeaders([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $streamable->mimetype(),
            'Content-Length' => $streamable->size(),
            'Content-Disposition' => $disposition,
            'X-Accel-Redirect' => $link,
        ]);
    }

    /**
     * Get the location of the nginx internal redirect.
     *
     * @return string
     */
    abstract protected function nginxRedirect(): string;
}
