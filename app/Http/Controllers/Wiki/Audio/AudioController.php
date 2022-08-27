<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Audio;

use App\Constants\Config\AudioConstants;
use App\Http\Controllers\Wiki\StreamableController;
use App\Models\Wiki\Audio;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class AudioController.
 */
class AudioController extends StreamableController
{
    /**
     * Stream audio through configured streaming method.
     *
     * @param  Audio  $audio
     * @return Response|StreamedResponse
     *
     * @throws RuntimeException
     */
    public function show(Audio $audio): Response|StreamedResponse
    {
        return match (Config::get(AudioConstants::STREAMING_METHOD_QUALIFIED)) {
            'response' => $this->throughResponse($audio),
            'nginx' => $this->throughNginx($audio),
            default => throw new RuntimeException('AUDIO_STREAMING_METHOD must be specified in your .env file'),
        };
    }

    /**
     * Get the filesystem disk that hosts the streamable model.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the location of the nginx internal redirect.
     *
     * @return string
     */
    protected function nginxRedirect(): string
    {
        return Config::get(AudioConstants::NGINX_REDIRECT_QUALIFIED);
    }
}
