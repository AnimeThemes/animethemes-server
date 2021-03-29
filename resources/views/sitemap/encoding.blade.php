<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('encoding.index') }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'audio_filtering']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'audio_normalization']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'colorspace']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'common_positions']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'ffmpeg']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'prereqs']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'setup']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'troubleshooting']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'utilities']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'verification']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'video_filtering']) }}</loc>
    </url>
    <url>
        <loc>{{ route('encoding.show', ['docName' => 'workflow']) }}</loc>
    </url>
</sitemapindex>
