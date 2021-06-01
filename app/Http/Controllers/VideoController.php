<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\StreamsContent;
use App\Models\Video;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class VideoController
 * @package App\Http\Controllers
 */
class VideoController extends Controller
{
    use StreamsContent;

    /**
     * Stream video.
     *
     * @param Video $video
     * @return StreamedResponse
     */
    public function show(Video $video): StreamedResponse
    {
        views($video)
            ->cooldown(now()->addMinutes(5))
            ->record();

        return $this->streamContent($video);
    }
}
