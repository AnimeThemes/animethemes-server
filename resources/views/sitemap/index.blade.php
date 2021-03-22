<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('welcome.index') }}</loc>
    </url>
    <url>
        <loc>{{ route('terms.show') }}</loc>
    </url>
    <url>
        <loc>{{ route('policy.show') }}</loc>
    </url>
    <url>
        <loc>{{ route('transparency.show') }}</loc>
    </url>
    <url>
        <loc>{{ route('donate.show') }}</loc>
    </url>
    <url>
        <loc>{{ route('faq.show') }}</loc>
    </url>
    <url>
        <loc>{{ route('l5-swagger.default.api') }}</loc>
    </url>
    <url>
        <loc>{{ url('wiki') }}</loc>
    </url>
    <sitemap>
        <loc>{{ route('sitemap.encoding') }}</loc>
    </sitemap>
    <sitemap>
        <loc>{{ route('sitemap.guidelines') }}</loc>
    </sitemap>
</sitemapindex>
