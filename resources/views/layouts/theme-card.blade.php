<div class="card theme-card gap-v-100" data-group="{{ $theme->group }}">
    <div class="row --center">
        <div class="theme-card__sequence">
            <small>{{ $theme->slug }}</small>
        </div>
        <div>
            <span class="theme-card__title">{{ $theme->song->title }}</span>
            @if ($theme->song->artists->isNotEmpty())
                <small> by </small>
                @foreach ($theme->song->artists as $artist)
                    <span class="theme-card__artist">{{ $artist->as ?: $artist->name  }}{{ !$loop->last ? ', ' : '' }}</span>
                @endforeach
            @endif
        </div>
    </div>
    @foreach ($theme->entries as $entry)
        <div class="row --center">
            <div class="theme-card__sequence --secondary">
                @if (!empty($entry->version))
                    <small>v{{ $entry->version }}</small>
                @endif
            </div>
            <div class="row gap-h-75">
                <div class="row --center">
                    <i class="fas fa-film prefix-25"></i>
                    <small>{{ $entry->episodes ?: "—" }}</small>
                </div>
                @if (!empty($entry->spoiler))
                    <div class="row --center">
                        <i class="fas fa-bomb prefix-25 warning"></i>
                        <small>Spoiler</small>
                    </div>
                @endif
                @if (!empty($entry->nsfw))
                    <div class="row --center">
                        <i class="fas fa-exclamation-triangle prefix-25 warning"></i>
                        <small>NSFW</small>
                    </div>
                @endif
            </div>
            <div class="col --1 theme-card__video-list">
                @foreach ($entry->videos as $video)
                    @include('layouts.video-badge', $video)
                @endforeach
            </div>
        </div>
    @endforeach
</div>
