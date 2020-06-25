@extends('layouts.app-new')

@section('content')

    @php
        $groups = array_unique(array_column($anime->themes->toArray(), 'group'));
    @endphp

    {{-- Provide MyAnimeList link for JS to resolve additional data --}}
    <input id="anime__id" type="hidden" value="{{ $anime->externalResources[0]->link }}">

    <h1>{{ $anime->name }}</h1>
    <div class="row">
        <div class="col --3 anime__sidebar">
            <img class="anime__cover" alt="Cover">
            <dl class="description-list">
                @if ($anime->synonyms->isNotEmpty())
                    <dt class="description-list__key">Alternative Titles</dt>
                    <dd class="description-list__value">
                        @foreach ($anime->synonyms as $synonym)
                            <div class="anime__synonym">{{ $synonym->text }}</div>
                        @endforeach
                    </dd>
                @endif
                <dt class="description-list__key">Premiere</dt>
                <dd class="description-list__value">
                    {{ $anime->season->description }} {{ $anime->year  }}
                </dd>
                @if ($anime->externalResources->isNotEmpty())
                    <dt class="description-list__key">Links</dt>
                    <dd class="description-list__value">
                        @foreach ($anime->externalResources as $resource)
                            <a href="{{ $resource->link }}">
                                <span>{{ $resource->type->description }}</span>
                                <sup>
                                    <i class="fas fa-external-link-alt"></i>
                                </sup>
                            </a>
                        @endforeach
                    </dd>
                @endif
            </dl>
        </div>
        <div class="col --9">
            <h2>Synopsis</h2>
            <div class="card --hoverable anime__synopsis --collapsed">
                <span class="anime__synopsis-text">
                    Here is where a synopsis could be...
                </span>
            </div>
            <h2>Themes</h2>
            <div class="gap-v-100">
                @if (count($groups) > 1)
                    <div class="row gap-h-100">
                        @foreach ($groups as $group)
                            <button class="button --primary @if ($loop->first) --active @endif anime__group-tab" data-group="{{ $group }}">
                                {{ $group }}
                            </button>
                        @endforeach
                    </div>
                @endif
                @foreach ($anime->themes as $theme)
                    @include('layouts.theme-card', $theme)
                @endforeach
            </div>
        </div>
    </div>

@endsection
