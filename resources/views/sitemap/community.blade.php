<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('community.index') }}</loc>
    </url>
    <url>
        <loc>{{ route('community.show', ['docName' => 'bitrate']) }}</loc>
    </url>
    <url>
        <loc>{{ route('community.show', ['docName' => 'pix_fmt']) }}</loc>
    </url>
    <url>
        <loc>{{ route('community.show', ['docName' => 'requests']) }}</loc>
    </url>
    <url>
        <loc>{{ route('community.show', ['docName' => 'vp9']) }}</loc>
    </url>
</sitemapindex>
