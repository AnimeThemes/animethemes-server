<?php

declare(strict_types=1);

namespace App\Actions\Http;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

abstract class NginxStreamAction extends StreamAction
{
    /**
     * Stream the resource.
     */
    public function stream(string $disposition = 'inline'): Response
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk($this->disk());

        $temporaryURL = $fs->temporaryUrl($this->streamable->path(), now()->addMinutes(5));

        // Get the url information
        $url_scheme = parse_url($temporaryURL, PHP_URL_SCHEME);
        $url_host = parse_url($temporaryURL, PHP_URL_HOST);
        $url_path_query = parse_url($temporaryURL, PHP_URL_PATH).'?'.parse_url($temporaryURL, PHP_URL_QUERY);

        $link = "{$this->nginxRedirect()}$url_scheme/$url_host$url_path_query";

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
     */
    abstract protected function nginxRedirect(): string;
}
