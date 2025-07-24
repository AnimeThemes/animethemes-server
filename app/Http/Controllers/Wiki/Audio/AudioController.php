<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Audio;

use App\Actions\Http\StreamAction;
use App\Actions\Http\Wiki\Audio\AudioNginxStreamAction;
use App\Actions\Http\Wiki\Audio\AudioResponseStreamAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Http\StreamingMethod;
use App\Http\Controllers\Controller;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class AudioController extends Controller
{
    /**
     * Stream audio through configured streaming method.
     *
     * @param  Audio  $audio
     * @param  Request  $request
     * @return Response
     */
    public function show(Audio $audio, Request $request): Response
    {
        /** @var StreamAction $action */
        $action = match (Config::get(AudioConstants::STREAMING_METHOD_QUALIFIED)) {
            StreamingMethod::RESPONSE->value => new AudioResponseStreamAction($audio),
            StreamingMethod::NGINX->value => new AudioNginxStreamAction($audio),
            default => throw new RuntimeException('AUDIO_STREAMING_METHOD must be specified in your .env file'),
        };

        // If the "download" query param is set we want to force the browser to download the file.
        // Otherwise, it should be shown inline for direct playback.
        $disposition = $request->has('download')
            ? 'attachment'
            : 'inline';

        return $action->stream($disposition);
    }
}
