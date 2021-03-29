<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('event.index') }}</loc>
    </url>
    <url>
        <loc>{{ route('event.show', ['docName' => 'best_ending_iii']) }}</loc>
    </url>
    <url>
        <loc>{{ route('event.show', ['docName' => 'best_ending_iv']) }}</loc>
    </url>
    <url>
        <loc>{{ route('event.show', ['docName' => 'best_ending_v']) }}</loc>
    </url>
    <url>
        <loc>{{ route('event.show', ['docName' => 'best_ending_vi']) }}</loc>
    </url>
    <url>
        <loc>{{ route('event.show', ['docName' => 'best_opening_vii']) }}</loc>
    </url>
    <url>
        <loc>{{ route('event.show', ['docName' => 'best_opening_viii']) }}</loc>
    </url>
</sitemapindex>
