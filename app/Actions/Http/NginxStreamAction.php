<?php

declare(strict_types=1);

namespace App\Actions\Http;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class NginxStreamAction.
 */
abstract class NginxStreamAction extends StreamAction
{
    /**
     * Stream the resource.
     *
     * @return Response
     */
    public function stream(): Response
    {
        $fs = Storage::disk($this->disk());

        // Generate temporary link for the object
        $temporaryURL = $fs->temporaryUrl($this->streamable->path(), now()->addMinutes(5));

        // Get the url information
        $url_scheme = parse_url($temporaryURL, PHP_URL_SCHEME);
        $url_host = parse_url($temporaryURL, PHP_URL_HOST);
        $url_path_query = parse_url($temporaryURL, PHP_URL_PATH).'?'.parse_url($temporaryURL, PHP_URL_QUERY);

        // Construct the new link for the redirect
        $link = "{$this->nginxRedirect()}$url_scheme/$url_host$url_path_query";

        $response = new Response();

        $disposition = $response->headers->makeDisposition('inline', $this->streamable->basename());

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
