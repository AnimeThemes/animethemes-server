<a class="badge --hoverable gap-h-75" href="{{ route('video.show', $video->basename) }}">
    @if (!empty($video->resolution))
        <div class="row --center">
            <i class="fas fa-arrows-alt-v prefix-25"></i>
            <small>{{ $video->resolution }}</small>
        </div>
    @endif
    @if (!empty($video->nc))
        <div class="row --center">
            <i class="fas fa-not-equal prefix-25"></i>
            <small>NC</small>
        </div>
    @endif
    @if (!empty($video->lyrics))
        <div class="row --center">
            <i class="fas fa-music prefix-25"></i>
            <small>Lyrics</small>
        </div>
    @endif
    @if (!empty($video->source))
        <div class="row --center">
            <i class="fas fa-compact-disc prefix-25"></i>
            <small>{{ $video->source->description }}</small>
        </div>
    @endif
    @if (!empty($video->overlap->value))
        <div class="row --center">
            <i class="fas fa-stream prefix-25"></i>
            <small>{{ $video->overlap->description }}</small>
        </div>
    @endif
</a>
