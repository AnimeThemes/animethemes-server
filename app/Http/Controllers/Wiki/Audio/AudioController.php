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
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AudioController.
 */
class AudioController extends Controller
{
    /**
     * Stream audio through configured streaming method.
     *
     * @param  Audio  $audio
     * @return Response
     *
     * @throws RuntimeException
     */
    public function show(Audio $audio): Response
    {
        /** @var StreamAction $action */
        $action = match (Config::get(AudioConstants::STREAMING_METHOD_QUALIFIED)) {
            StreamingMethod::RESPONSE->value => new AudioResponseStreamAction($audio),
            StreamingMethod::NGINX->value => new AudioNginxStreamAction($audio),
            default => throw new RuntimeException('AUDIO_STREAMING_METHOD must be specified in your .env file'),
        };

        return $action->stream();
    }
}
