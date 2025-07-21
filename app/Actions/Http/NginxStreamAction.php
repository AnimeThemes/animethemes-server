<?php

declare(strict_types=1);

namespace App\Actions\Http;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Uri;

/**
 * Class NginxStreamAction.
 */
abstract class NginxStreamAction extends StreamAction
{
    /**
     * Stream the resource.
     *
     * @param  string  $disposition
     * @return Response
     */
    public function stream(string $disposition = 'inline'): Response
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk($this->disk());

        // Generate temporary link for the object
        $temporaryURL = Uri::of($fs->temporaryUrl($this->streamable->path(), now()->addMinutes(5)));

        // Construct the new link for the redirect
        $link = Uri::of()
            ->withHost($temporaryURL->host())
            ->withScheme($temporaryURL->scheme())
            ->withPath($temporaryURL->path())
            ->withQuery($temporaryURL->query()->toArray())
            ->__toString();

        $link = "{$this->nginxRedirect()}$link";

        $response = new Response();

        $disposition = $response->headers->makeDisposition($disposition, $this->streamable->basename());

        return $response->withHeaders([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $this->streamable->mimetype(),
            'Content-Length' => $this->streamable->size(),
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
